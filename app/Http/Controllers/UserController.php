<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Models\PaymentOrder;
use App\Models\Transaction;

class UserController extends Controller
{
    public function viewPage($page, $id = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $viewName = 'user.' . $page;
        if (!view()->exists($viewName)) {
            abort(404, 'Halaman tidak ditemukan.');
        }

        $book = $id ? Book::find($id) : null;

        return view($viewName, compact('book', 'id'));
    }

    public function readBook($id)
    {
        $book = Book::findOrFail($id);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk membaca.');
        }

        $userId = Auth::id();
        $user = Auth::user();
        // 1. Cek apakah buku gratis
        $isFree = isset($book->price) && $book->price == 0;
        $isAdmin = strtolower($user->role ?? '') === 'admin'
            || ($user->email ?? null) === 'rafibadillah8@gmail.com';

        // Only a transaction for this exact book grants purchased access.
        $hasPurchasedInDb = Transaction::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->exists()
            || PaymentOrder::where('user_id', $userId)
                ->where('book_id', $book->id)
                ->where('status', 'paid')
                ->exists();

        $hasPurchased = $isAdmin || $hasPurchasedInDb;

        // 3. Jika bukan buku gratis DAN belum ada bukti pembelian di database, tolak
        if (!$isFree && !$hasPurchased) {
            return redirect()->route('books.show', $id)
                ->with('error', 'Anda harus membeli buku ini terlebih dahulu.');
        }

        // Deteksi lokasi view halaman reader secara dinamis
        if (view()->exists('user.books.read')) {
            $viewName = 'user.books.read';
        } elseif (view()->exists('user.read')) {
            $viewName = 'user.read';
        } elseif (view()->exists('books.read')) {
            $viewName = 'books.read';
        } else {
            $viewName = 'admin.books.read';
        }

        return view($viewName, compact('book'));
    }
}