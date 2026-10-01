<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Book;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\PaymentOrder;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{
    public function getSnapToken(Request $request, $id)
    {
        $serverKey = trim(config('services.midtrans.server_key'));
        $isProduction = config('services.midtrans.is_production', false);
        
        $user = Auth::user();
        $book = Book::findOrFail($id);

        $orderId = 'RP-' . $book->id . '-' . time() . '-' . random_int(100, 999);
        $voucher = null;
        $voucherCode = $request->input('voucher_code') ?: session('active_voucher_code');
        if ($voucherCode) {
            $voucher = Voucher::where('code', strtoupper($voucherCode))->first();
        }
        $bookPrice = $voucher && $voucher->isValid() && $voucher->appliesTo($book)
            ? $voucher->calculateDiscount($book->price)
            : $book->price;

        $url = $isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        // URL callback khusus yang akan menyimpan transaksi ke DB
        $callbackUrl = route('payment.callback', $book->id);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $bookPrice,
            ],
            'item_details' => [
                [
                    'id' => 'BOOK-' . $book->id,
                    'price' => $bookPrice,
                    'quantity' => 1,
                    'name' => mb_strimwidth($book->title, 0, 45, '...')
                ]
            ],
            'customer_details' => [
                'first_name' => $user ? $user->name : 'Tamu Ruang Pustaka',
                'email' => $user ? $user->email : 'tamu@example.com',
            ],
            'callbacks' => [
                'finish'   => $callbackUrl,
                'unfinish' => $callbackUrl,
                'error'    => $callbackUrl,
            ]
        ];

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'snap_token' => null,
                    'message' => 'Pembayaran sedang tidak tersedia. Silakan coba lagi.'
                ], 200);
            }

            $body = $response->json();

            if (isset($body['token'])) {
                PaymentOrder::create([
                    'order_id' => $orderId,
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'price' => (int) $bookPrice,
                    'voucher_code' => $voucher && $voucher->isValid() ? $voucher->code : null,
                    'status' => 'pending',
                ]);

                session(['final_price_' . $book->id => $bookPrice]);
                $pendingOrders = session('midtrans_pending_' . $book->id, []);
                $pendingOrders[] = [
                    'order_id' => $orderId,
                    'price' => (int) $bookPrice,
                    'voucher_code' => $voucher && $voucher->isValid() ? $voucher->code : null,
                    'created_at' => time(),
                ];
                session()->put('midtrans_pending_' . $book->id, array_slice($pendingOrders, -5));
                session()->save();

                return response()->json([
                    'success' => true,
                    'snap_token' => $body['token']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'snap_token' => null,
                    'message' => 'MIDTRANS API RESPONSE: Token tidak ditemukan dalam respons.'
                ], 200);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false, 
                'snap_token' => null,
                'message' => 'Pembayaran sedang tidak tersedia. Silakan coba lagi.'
            ], 200);
        }
    }

    /**
     * Midtrans redirect ke sini setelah pembayaran selesai.
     * Simpan transaksi ke database, lalu arahkan user ke halaman buku.
     */
    public function paymentCallback(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $orderId = $request->input('order_id');
        if (!$orderId) {
            $orderId = PaymentOrder::where('user_id', $userId)
                ->where('book_id', $book->id)
                ->where('status', 'pending')
                ->latest()
                ->value('order_id');
        }
        if (!$orderId) {
            $legacyPendingOrder = collect(session('midtrans_pending_' . $book->id, []))
                ->reverse()
                ->first();
            $orderId = $legacyPendingOrder['order_id'] ?? null;
        }
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
            return redirect()->route('books.show', $id)
                ->with('error', 'Pesanan pembayaran tidak ditemukan.');
        }

        try {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
            \Midtrans\Config::$curlOptions = [
                CURLOPT_CAINFO => 'D:/laragon/etc/ssl/cacert.pem',
                CURLOPT_HTTPHEADER => [],
            ];
            $status = json_decode(json_encode(\Midtrans\Transaction::status($orderId)), true);
        } catch (\Throwable $e) {
            Log::error('Midtrans status verification failed.', [
                'order_id' => $orderId,
                'exception' => $e,
            ]);
            return redirect()->route('books.show', $id)
                ->with('error', 'Status pembayaran belum dapat diverifikasi.');
        }

        $transactionStatus = $status['transaction_status'] ?? '';
        $fraudStatus = $status['fraud_status'] ?? 'accept';
        if (!($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept'))) {
            $message = $transactionStatus === 'pending'
                ? 'Pembayaran Anda masih pending. Akses akan dibuka setelah pembayaran dikonfirmasi.'
                : 'Pembayaran belum berhasil diverifikasi.';
            return redirect()->route('books.show', $id)->with('error', $message);
        }

        $alreadyRecorded = Transaction::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->exists();

        // Ambil harga final (setelah diskon voucher, jika ada) yang disimpan
        // sebelumnya di getSnapToken(). Fallback ke harga asli jika tidak ada.
        $finalPrice = (int) $pendingOrder['price'];
        $voucherCode = $pendingOrder['voucher_code']
            ?? session('active_voucher_code');

        if (!$alreadyRecorded) {
            Transaction::create([
                'user_id' => $userId,
                'book_id' => $book->id,
                'title'   => trim($book->title),
                'author'  => $book->author,
                'price'   => $finalPrice,
            ]);

            // Tandai voucher sebagai terpakai (increment used_count)
            if ($voucherCode) {
                VoucherController::useVoucherCode($voucherCode);
            }
        }

        if ($paymentOrder) {
            $paymentOrder->update(['status' => 'paid']);
        }

        session()->put('purchased_books_' . $id, true);
        session()->forget('midtrans_pending_' . $book->id);
        session()->forget('final_price_' . $id);
        session()->forget('active_voucher_code');
        session()->save();

        return redirect()->route('books.show', $id)
            ->with('success', 'Pembayaran berhasil! Akses membaca telah dibuka.');
    }
}