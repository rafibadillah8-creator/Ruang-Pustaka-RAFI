<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('book')->where('user_id', Auth::id())->latest()->get();
        return view('user.wishlist', compact('wishlists'));
    }

    public function toggle($book_id)
    {
        $user_id = Auth::id();
        $wishlist = Wishlist::where('user_id', $user_id)->where('book_id', $book_id)->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Buku dihapus dari wishlist!');
        } else {
            Wishlist::create([
                'user_id' => $user_id,
                'book_id' => $book_id,
            ]);
            return back()->with('success', 'Buku ditambahkan ke wishlist!');
        }
    }
}
