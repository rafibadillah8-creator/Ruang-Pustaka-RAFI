<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Voucher;
use App\Models\Transaction;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'book_id' => 'required|exists:books,id',
        ]);

        $voucher = Voucher::where('code', strtoupper($request->code))->first();
        $book = Book::findOrFail($request->book_id);
        $user = auth()->user();

        $alreadyPurchased = Transaction::where('user_id', $user->id)
            ->where('title', $book->title)
            ->exists() || session()->has('purchased_books_' . $book->id);

        if ($alreadyPurchased) {
            return response()->json([
                'success' => false, 
                'message' => 'Kamu sudah membeli buku ini sebelumnya. Silakan baca langsung!'
            ], 422);
        }

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode voucher tidak ditemukan.'], 404);
        }

        if (!$voucher->isValid()) {
            return response()->json(['success' => false, 'message' => 'Voucher sudah tidak berlaku atau kadaluarsa.'], 422);
        }

        if (!$voucher->appliesTo($book)) {
            return response()->json(['success' => false, 'message' => 'Voucher ini tidak berlaku untuk buku ini.'], 422);
        }

        $originalPrice = $book->price;
        $finalPrice = $voucher->calculateDiscount($originalPrice);

        session(['active_voucher_code' => $voucher->code]);
        session()->save();

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'original_price' => $originalPrice,
            'final_price' => $finalPrice,
            'discount_label' => $voucher->type === 'percentage'
                ? $voucher->value . '%'
                : 'Rp ' . number_format($voucher->value, 0, ',', '.'),
        ]);
    }

    public static function useVoucherCode($code)
    {
        if (empty($code)) return;

        $voucher = Voucher::where('code', strtoupper($code))->first();
        if ($voucher) {
            $voucher->increment('used_count');
        }
    }
}
