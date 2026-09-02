<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        // Pencarian (Judul atau Penulis)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // Filter Berdasarkan Kategori
        if ($request->filled('category')) {
            $category = $request->category;
            $query->where('category', 'like', "%{$category}%");
        }

        // Pengurutan (Popularitas atau Terbaru)
        if ($request->sort === 'popular') {
            $query->orderBy('views', 'desc');
        } else {
            $query->latest();
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $books */
        $books = $query->paginate(12)->appends($request->query());

        // Ambil kategori untuk filter dropdown
        try {
            $categories = Category::all();
        } catch (\Exception $e) {
            $categories = collect();
        }

        return view('user.books.index', compact('books', 'categories'));
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);

        return view('user.books.show', compact('book'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        // Validasi data yang masuk
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|array', 
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Ubah array checkbox kategori menjadi string dipisahkan koma
        $validated['category'] = implode(', ', $request->category);

        // Handle upload file cover baru
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        // Simpan ke database
        $book->update($validated);

        // Redirect kembali ke halaman admin dashboard dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'Data buku berhasil diperbarui!');
    }
}