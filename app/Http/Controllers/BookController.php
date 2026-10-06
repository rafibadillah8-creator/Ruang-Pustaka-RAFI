<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Models\PaymentOrder;
use App\Services\MidtransGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
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
            $isPurchased = Transaction::where('user_id', $userId)
                ->where('book_id', $book->id)
                ->exists()
                || PaymentOrder::where('user_id', $userId)
                    ->where('book_id', $book->id)
                    ->where('status', 'paid')
                    ->exists();
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
            if (!$paymentOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan pembayaran tidak ditemukan.'
                ], 422);
            }

            try {
                app(MidtransGateway::class)->configure();
                $status = json_decode(json_encode(\Midtrans\Transaction::status($orderId)), true);
            } catch (\Throwable $e) {
                report($e);
                return response()->json([
                    'success' => false,
                    'message' => 'Status pembayaran belum dapat diverifikasi.'
                ], 502);
            }

            try {
                app(\App\Services\PaymentOrderFulfillment::class)->applyStatus($paymentOrder, $status);
            } catch (\InvalidArgumentException $e) {
                report($e);
                return response()->json([
                    'success' => false,
                    'message' => 'Nominal pembayaran tidak sesuai dengan pesanan.'
                ], 422);
            }

            if ($paymentOrder->fresh()->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran belum berhasil diverifikasi.'
                ], 422);
            }

            session()->forget('active_voucher_code');

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat.'
            ]);
        }

        $alreadyPurchased = Transaction::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->exists()
            || PaymentOrder::where('user_id', $userId)
                ->where('book_id', $book->id)
                ->where('status', 'paid')
                ->exists();

        if ($alreadyPurchased) {
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
                app(MidtransGateway::class)->configure();

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
                Log::error('Midtrans Snap token creation failed.', [
                    'book_id' => $book->id,
                    'user_id' => $userId,
                    'exception' => $e,
                ]);

                return response()->json([
                    'success' => false,
                    'snap_token' => null,
                    'message' => $e instanceof \InvalidArgumentException
                        ? $e->getMessage()
                        : 'Pembayaran tidak dapat dimulai. Periksa konfigurasi Midtrans atau coba lagi.'
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
            ->where('book_id', $book->id)
            ->exists()
            || PaymentOrder::where('user_id', $userId)
                ->where('book_id', $book->id)
                ->where('status', 'paid')
                ->exists();

        if ($existingTransaction) {
            return response()->json([
                'success' => true,
                'pending' => false,
                'message' => 'Pembelian sudah tercatat.'
            ]);
        }

        // Persisted payment orders are the only source of payment state.
        $databaseOrders = PaymentOrder::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->whereNotIn('status', ['failed', 'paid'])
            ->where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->get()
            ->reverse()
            ->values();
        $orders = $databaseOrders;

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'pending' => false,
                'terminal_failure' => false,
                'message' => 'Tidak ada pembayaran yang menunggu.'
            ], 200);
        }

        try {
            app(MidtransGateway::class)->configure();
        } catch (Exception $e) {
            Log::error('Midtrans payment status check could not be configured.', [
                'book_id' => $book->id,
                'user_id' => $userId,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'pending' => true,
                'message' => $e instanceof \InvalidArgumentException
                    ? $e->getMessage()
                    : 'Status pembayaran belum dapat diverifikasi.'
            ], 503);
        }

        $paidOrder = null;
        $lastStatus = null;
        $hasPendingOrder = false;

        foreach ($orders as $order) {
            try {
                $result = json_decode(json_encode(\Midtrans\Transaction::status($order->order_id)), true);
            } catch (Exception $e) {
                Log::warning('Midtrans payment status lookup failed.', [
                    'order_id' => $order->order_id,
                    'exception' => $e,
                ]);
                if ($order->created_at->greaterThan(now()->subHours(24))) {
                    $hasPendingOrder = true;
                }
                continue;
            }

            $trxStatus = $result['transaction_status'] ?? '';
            $lastStatus = $trxStatus;

            try {
                app(\App\Services\PaymentOrderFulfillment::class)->applyStatus($order, $result);
            } catch (\InvalidArgumentException $e) {
                Log::warning('Midtrans returned an amount that does not match the payment order.', [
                    'order_id' => $order->order_id,
                    'exception' => $e,
                ]);
                continue;
            }

            if ($order->fresh()->status === 'paid') {
                $paidOrder = $order;
                break;
            }

            if (in_array($trxStatus, ['pending', 'authorize', 'capture'], true)
                || $order->created_at->greaterThan(now()->subHours(24))) {
                $hasPendingOrder = true;
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

        session()->forget('active_voucher_code');

        return response()->json([
            'success' => true,
            'pending' => false,
            'message' => 'Pembayaran terkonfirmasi. Akses baca telah dibuka.'
        ]);
    }
}