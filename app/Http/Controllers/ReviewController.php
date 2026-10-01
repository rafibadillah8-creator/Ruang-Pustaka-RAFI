<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user();

        Review::create([
            'book_id'   => $book->id,
            'user_id'   => $user->id,
            'user_name' => $user->name,
            'user_role' => 'Pembaca',
            'rating'    => $request->rating,
            'comment'   => $request->comment,
        ]);

        return back()->with('success', 'Ulasan berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Hanya pemilik ulasan atau admin utama yang boleh hapus
        if ($review->user_id !== Auth::id() && Auth::user()->email !== 'rafibadillah8@gmail.com') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}