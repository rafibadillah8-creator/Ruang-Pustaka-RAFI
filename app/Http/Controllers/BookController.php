<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Models\PaymentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Midtrans\Config;
use Midtrans\Snap;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('categories');

        // 1. Pencarian (Judul atau Penulis)
        $search = $request->input('search', $request->input('q'));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // 2. Filter Berdasarkan Kategori
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        } elseif ($request->filled('category')) {
            $category = $request->category;
            $query->whereHas('categories', function($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }

        // 3. Pengurutan Data
        switch ($request->sort) {
            case 'popular':
            case 'populer':
                $query->latest(); // Fallback aman jika kolom views belum dimigrasi
                break;
            case 'harga_tertinggi':
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'harga_terendah':
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'terbaru':
            case 'latest':
            default:
                $query->latest();
                break;
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $books */
        $books = $query->paginate(12)->appends($request->query());

        // Ambil data kategori
        try {
            $categories = Category::all();
        } catch (\Exception $e) {
            $categories = collect();
        }

        // Ambil voucher aktif
        try {
            $activeVoucher = Voucher::where(function($q) {
                $q->where('status', 'aktif')
                  ->orWhere('is_active', true);
            })->latest()->first();
        } catch (\Exception $e) {
            $activeVoucher = null;
        }

        // Pengecekan lokasi view katalog secara dinamis
        if (view()->exists('user.books.index')) {
            $viewName = 'user.books.index';
        } elseif (view()->exists('books.index')) {
            $viewName = 'books.index';
        } else {
            $viewName = 'welcome';
        }

        return view($viewName, compact('books', 'categories', 'activeVoucher'));
    }

    public function searchSuggestions(Request $request)
    {
        $q = trim($request->input('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $books = Book::with('categories')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('author', 'like', "%{$q}%")
                      ->orWhereHas('categories', function ($cat) use ($q) {
                          $cat->where('name', 'like', "%{$q}%");
                      });
            })
            ->take(6)
            ->get()
            ->map(function ($book) {
                $cover = $book->cover_image && $book->cover_image !== 'default_cover.jpg'
                    ? asset('storage/' . $book->cover_image)
                    : asset('images/default_cover.jpg');

                return [
                    'id'     => $book->id,
                    'title'  => $book->title,
                    'author' => $book->author,
                    'url'    => route('books.show', $book->id),
                    'cover'  => $cover,
                ];
            });

        return response()->json($books);
    }

    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:255',
            'publisher'      => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'categories'     => 'nullable|array',
            'categories.*'   => 'exists:categories,id',
            'category_ids'   => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'cover_image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file_pdf'       => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        } else {
            $validated['cover_image'] = 'default_cover.jpg';
        }

        if ($request->hasFile('file_pdf')) {
            $validated['file_path'] = $request->file('file_pdf')->store('books', 'public');
        } elseif (!isset($validated['file_path'])) {
            $validated['file_path'] = '';
        }

        $bookData = collect($validated)->except(['categories', 'category_ids', 'file_pdf'])->toArray();
        $categoryIds = $request->input('category_ids', $request->input('categories', []));
        
        if (!empty($categoryIds)) {
            $bookData['category_id'] = $categoryIds[0];
        } else {
            $firstCategory = Category::first();
            if (!$firstCategory) {
                $firstCategory = Category::create(['name' => 'Umum']);
            }
            $bookData['category_id'] = $firstCategory->id; 
        }

        $book = Book::create($bookData);

        if (!empty($categoryIds)) {
            $book->categories()->sync($categoryIds);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Buku baru berhasil ditambahkan!');
    }

    public function show(Request $request, $id)
    {
        $book = Book::with('categories')->findOrFail($id);

        // Cek apakah user yang login merupakan Admin
        $isAdmin = auth()->check() && (
            strtolower(auth()->user()->role ?? '') === 'admin' || 
            auth()->user()->usertype === 'admin' || 
            auth()->user()->is_admin ||
            auth()->user()->email === 'rafibadillah8@gmail.com'
        );

        $isPurchased = false;
        
        // Jika Admin, otomatis anggap buku sudah "dibeli" / terbuka akses bacanya
        if ($isAdmin) {
            $isPurchased = true;
        } elseif (auth()->check()) {
            $userId = auth()->id();
            $cleanTitle = trim($book->title);

            $isPurchased = Transaction::where('user_id', $userId)
                ->where(function ($q) use ($book, $cleanTitle) {
                    $q->where('book_id', $book->id)
                      ->orWhere('title', 'like', '%' . $cleanTitle . '%');
                })
                ->exists() 
                || PaymentOrder::where('user_id', $userId)->where('book_id', $book->id)->where('status', 'paid')->exists()
                || session()->has('purchased_books_' . $id);
        }

        if (isset($book->price) && $book->price == 0) {
            $isPurchased = true;
        }

        // Ambil voucher aktif untuk banner slider
        try {
            $activeVoucher = Voucher::where(function($q) {
                $q->where('status', 'aktif')
                  ->orWhere('is_active', true);
            })->latest()->first();
        } catch (\Exception $e) {
            $activeVoucher = null;
        }

        if (view()->exists('user.books.show')) {
            $viewName = 'user.books.show';
        } elseif (view()->exists('books.show')) {
            $viewName = 'books.show';
        } else {
            $viewName = 'admin.books.show';
        }

        return view($viewName, compact('book', 'id', 'isPurchased', 'activeVoucher'));
    }

    public function edit($id)
    {
        $book = Book::with('categories')->findOrFail($id);
        $categories = Category::all();

        return view('books.edit', compact('book', 'categories'));
    }

    public function getJsonData($id)
    {
        $book = Book::with('categories')->findOrFail($id);
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'book' => $book,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'author'         => 'required|string|max:255',
            'publisher'      => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'categories'     => 'nullable|array',
            'categories.*'   => 'exists:categories,id',
            'category_ids'   => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'cover_image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file_pdf'       => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && $book->cover_image !== 'default_cover.jpg' && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        if ($request->hasFile('file_pdf')) {
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }
            $validated['file_path'] = $request->file('file_pdf')->store('books', 'public');
        }

        $bookData = collect($validated)->except(['categories', 'category_ids', 'file_pdf'])->toArray();
        $book->update($bookData);

        $categoryIds = $request->input('category_ids', $request->input('categories', []));
        if (!empty($categoryIds)) {
            $book->categories()->sync($categoryIds);
        } else {
            $book->categories()->detach();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data buku berhasil diperbarui!'
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover_image && $book->cover_image !== 'default_cover.jpg' && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
        }

        $book->categories()->detach();
        $book->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Buku berhasil dihapus!');
    }

    public function processBuy(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $userId = auth()->id();
        $cleanTitle = trim($book->title);

        // Konfirmasi dari Snap harus diproses lebih dulu daripada flag session.
        // Callback/redirect Midtrans dapat tiba hampir bersamaan dengan request ini.
        $voucherCode = $request->input('voucher_code') ?: $request->input('code');
        if (empty($voucherCode)) {
            $voucherCode = session('active_voucher_code');
        }

        $paidPrice = $book->price;
        $usedVoucher = null;

        if (!empty($voucherCode)) {
            $usedVoucher = Voucher::where('code', strtoupper($voucherCode))->first();

            if ($usedVoucher && $usedVoucher->isValid()) {
                $paidPrice = $usedVoucher->calculateDiscount($book->price);
            }
        }

        if ($request->wantsJson() && $request->input('payment_completed')) {
            $orderId = $request->input('order_id');
            $paymentOrder = $orderId
                ? PaymentOrder::where('order_id', $orderId)
                    ->where('user_id', $userId)
                    ->where('book_id', $book->id)
                    ->first()
                : null;
            $pendingOrder = $paymentOrder?->only(['order_id', 'price', 'voucher_code'])
                ?? collect(session('midtrans_pending_' . $book->id, []))
                    ->reverse()
                    ->first(function ($order) use ($orderId) {
                        return $orderId && ($order['order_id'] ?? null) === $orderId;
                    });

            if (!$pendingOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan pembayaran tidak ditemukan.'
                ], 422);
            }

            try {
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = config('services.midtrans.is_production', false);
                Config::$curlOptions = [
                    CURLOPT_CAINFO => 'D:/laragon/etc/ssl/cacert.pem',
                    CURLOPT_HTTPHEADER => [],
                ];
                $status = json_decode(json_encode(\Midtrans\Transaction::status($orderId)), true);
            } catch (\Throwable $e) {
                report($e);
                return response()->json([
                    'success' => false,
                    'message' => 'Status pembayaran belum dapat diverifikasi.'
                ], 502);
            }

            $transactionStatus = $status['transaction_status'] ?? '';
            $fraudStatus = $status['fraud_status'] ?? 'accept';
            if (!($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran belum berhasil diverifikasi.'
                ], 422);
            }

            $paidPrice = (int) $pendingOrder['price'];
            $voucherCode = $pendingOrder['voucher_code'] ?? null;

            $transaction = Transaction::firstOrCreate(
                [
                    'user_id' => $userId,
                    'book_id' => $book->id,
                ],
                [
                    'title' => $cleanTitle,
                    'author' => $book->author,
                    'price' => $paidPrice,
                ]
            );

            if ($transaction->wasRecentlyCreated) {
                if ($voucherCode) {
                    VoucherController::useVoucherCode($voucherCode);
                }
                session()->forget('active_voucher_code');
            }

            if ($paymentOrder) {
                $paymentOrder->update(['status' => 'paid']);
            }

            session()->forget('midtrans_pending_' . $book->id);
            session()->put('purchased_books_' . $id, true);
            session()->save();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat.'
            ]);
        }

        $alreadyPurchased = Transaction::where('user_id', $userId)
            ->where(function ($q) use ($book, $cleanTitle) {
                $q->where('book_id', $book->id)
                  ->orWhere('title', 'like', '%' . $cleanTitle . '%');
            })
            ->exists() || session()->has('purchased_books_' . $id);

        if ($alreadyPurchased) {
            session()->put('purchased_books_' . $id, true);
            session()->save();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'snap_token' => null,
                    'message' => 'Anda sudah membeli buku ini sebelumnya.'
                ], 200);
            }

            return redirect()->route('books.show', $id)->with('info', 'Anda sudah membeli buku ini sebelumnya.');
        }

        if ($request->wantsJson()) {
            try {
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = config('services.midtrans.is_production', false);
                Config::$isSanitized = true;
                Config::$is3ds = true;

                // Arahkan cURL ke file sertifikat SSL yang benar (Laragon ada di drive D:)
                Config::$curlOptions = [
                    CURLOPT_CAINFO => 'D:/laragon/etc/ssl/cacert.pem',
                    CURLOPT_HTTPHEADER => [],
                ];

                $orderId = 'RUANGPUSTAKA-' . $book->id . '-' . now()->format('YmdHisv') . '-' . random_int(100000, 999999);

                $transaction_details = [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $paidPrice,
                ];

                $item_details = [
                    [
                        'id' => 'BOOK-' . $book->id,
                        'price' => (int) $paidPrice,
                        'quantity' => 1,
                        'name' => $book->title
                    ]
                ];

                $user = auth()->user();
                $customer_details = [
                    'first_name' => $user ? $user->name : 'Tamu Ruang Pustaka',
                    'email' => $user ? $user->email : 'tamu@example.com',
                ];

                $transaction = [
                    'transaction_details' => $transaction_details,
                    'item_details' => $item_details,
                    'customer_details' => $customer_details,
                    'callbacks' => [
                        'finish' => route('payment.callback', $book->id),
                        'unfinish' => route('payment.callback', $book->id),
                        'error' => route('payment.callback', $book->id),
                    ],
                ];

                $snapToken = Snap::getSnapToken($transaction);

                PaymentOrder::create([
                    'order_id' => $orderId,
                    'user_id' => $userId,
                    'book_id' => $book->id,
                    'price' => (int) $paidPrice,
                    'voucher_code' => ($usedVoucher && $usedVoucher->isValid()) ? $usedVoucher->code : null,
                    'status' => 'pending',
                ]);

                $pendingOrders = session('midtrans_pending_' . $book->id, []);
                $pendingOrders[] = [
                    'order_id'     => $orderId,
                    'price'        => (int) $paidPrice,
                    'voucher_code' => ($usedVoucher && $usedVoucher->isValid()) ? $usedVoucher->code : null,
                    'created_at'   => time(),
                ];
                session()->put('midtrans_pending_' . $book->id, array_slice($pendingOrders, -5));
                session()->save();

                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'snap_token' => null,
                    'message' => 'MIDTRANS ERROR: ' . $e->getMessage()
                ], 200);
            }
        }

        // ============================================================
        // KEAMANAN: Blokir semua request non-JSON/non-AJAX.
        // Seluruh alur pembayaran Midtrans menggunakan AJAX (wantsJson).
        // Jika ada yang mencoba kirim form POST biasa ke sini,
        // tolak dengan tegas agar tidak bisa bypass pembayaran.
        // ============================================================
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak valid.'
            ], 400);
        }

        return redirect()->route('books.show', $id)
            ->with('error', 'Permintaan tidak valid. Gunakan tombol Beli yang tersedia.');
    }

    /**
     * Cek status pembayaran ke Midtrans (Status API) untuk order yang pernah dibuat user ini.
     * Dipakai saat pembayaran baru lunas SETELAH popup ditutup (mis. simulasi VA/QRIS di sandbox),
     * sehingga callback onSuccess di browser tidak pernah terpanggil dan transaksi belum tercatat.
     * Akses baca hanya dibuka kalau Midtrans sendiri yang mengonfirmasi lunas (settlement/capture).
     */
    public function checkPayment(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'pending' => false, 'message' => 'Belum login.'], 200);
        }

        $book = Book::findOrFail($id);
        $userId = auth()->id();

        $existingTransaction = Transaction::where('user_id', $userId)
            ->where(function ($q) use ($book) {
                $q->where('book_id', $book->id)
                  ->orWhere('title', 'like', '%' . trim($book->title) . '%');
            })
            ->exists() || PaymentOrder::where('user_id', $userId)->where('book_id', $book->id)->where('status', 'paid')->exists();

        if ($existingTransaction) {
            session()->put('purchased_books_' . $id, true);
            return response()->json([
                'success' => true,
                'pending' => false,
                'message' => 'Pembelian sudah tercatat.'
            ]);
        }

        // Ambil order yang dibuat dalam 24 jam terakhir yang statusnya bukan failed / paid
        $databaseOrders = PaymentOrder::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->whereNotIn('status', ['failed', 'paid'])
            ->where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->get()
            ->map(fn (PaymentOrder $order) => [
                'order_id' => $order->order_id,
                'price' => $order->price,
                'voucher_code' => $order->voucher_code,
                'created_at' => $order->created_at->timestamp,
            ]);

        $sessionOrders = collect(session('midtrans_pending_' . $book->id, []))
            ->filter(fn ($o) => !empty($o['order_id']) && (time() - ($o['created_at'] ?? 0)) < 86400)
            ->reject(fn ($sessionOrder) => PaymentOrder::where('order_id', $sessionOrder['order_id'])->where('status', 'paid')->exists())
            ->reject(fn ($sessionOrder) => $databaseOrders->contains('order_id', $sessionOrder['order_id']));
        $orders = $databaseOrders->concat($sessionOrders)->reverse()->values();

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'pending' => false,
                'terminal_failure' => false,
                'message' => 'Tidak ada pembayaran yang menunggu.'
            ], 200);
        }

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$curlOptions = [
            CURLOPT_CAINFO => 'D:/laragon/etc/ssl/cacert.pem',
            CURLOPT_HTTPHEADER => [],
        ];

        $paidOrder = null;
        $lastStatus = null;
        $hasPendingOrder = false;

        foreach ($orders as $order) {
            try {
                $result = json_decode(json_encode(\Midtrans\Transaction::status($order['order_id'])), true);
            } catch (Exception $e) {
                // Jika order baru dibuat (< 2 jam), 404 dari Midtrans berarti pengguna belum men-submit metode bayar di Snap/Sandbox.
                // Tetap tandai sebagai pending agar polling mengecek sampai pembayaran diselesaikan.
                $orderAge = time() - ($order['created_at'] ?? 0);
                if ($orderAge < 7200) {
                    $hasPendingOrder = true;
                }
                continue;
            }

            $trxStatus = $result['transaction_status'] ?? '';
            $fraudStatus = $result['fraud_status'] ?? 'accept';
            $lastStatus = $trxStatus;

            if ($trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept')) {
                $paidOrder = $order;
                break;
            }

            if (in_array($trxStatus, ['pending', 'authorize', 'capture'], true)) {
                $hasPendingOrder = true;
            } elseif (in_array($trxStatus, ['cancel', 'deny', 'expire', 'failure'], true)) {
                PaymentOrder::where('order_id', $order['order_id'])
                    ->where('user_id', $userId)
                    ->where('book_id', $book->id)
                    ->update(['status' => 'failed']);
            }
        }

        if (!$paidOrder) {
            return response()->json([
                'success' => false,
                'pending' => $hasPendingOrder,
                'terminal_failure' => false,
                'status'  => $lastStatus,
                'message' => $hasPendingOrder
                    ? 'Pembayaran belum lunas.'
                    : 'Belum ada pembayaran yang berhasil.'
            ], 200);
        }

        $paidOrderRecord = PaymentOrder::where('order_id', $paidOrder['order_id'])
            ->where('user_id', $userId)
            ->where('book_id', $book->id)
            ->first();

        $alreadyRecorded = Transaction::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->exists();

        if (!$alreadyRecorded) {
            Transaction::create([
                'user_id' => $userId,
                'book_id' => $book->id,
                'title'   => trim($book->title),
                'author'  => $book->author,
                'price'   => $paidOrder['price'] ?? $book->price,
            ]);

            if (!empty($paidOrder['voucher_code'])) {
                $voucher = Voucher::where('code', $paidOrder['voucher_code'])->first();
                if ($voucher) {
                    $voucher->increment('used_count');
                }
            }
        }

        if ($paidOrderRecord) {
            $paidOrderRecord->update(['status' => 'paid']);
        }

        session()->forget('midtrans_pending_' . $book->id);
        session()->forget('active_voucher_code');
        session()->put('purchased_books_' . $id, true);
        session()->save();

        return response()->json([
            'success' => true,
            'pending' => false,
            'message' => 'Pembayaran terkonfirmasi. Akses baca telah dibuka.'
        ]);
    }
}