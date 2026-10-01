<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Buku Populer
        $popularBooks = Book::latest()->take(10)->get();

        // 2. Buku Baru (Upload < 24 jam / 1 hari)
        $newBooks = Book::where('created_at', '>=', Carbon::now()->subHours(24))
                        ->latest()
                        ->get();

        // 3. Kategori beserta Buku di dalamnya
        // (Pastikan relasi 'books' sudah ada di Model Category, jika belum ada relasi kategori akan melewati bagian ini)
        $categories = class_exists(Category::class) 
            ? Category::has('books')->with(['books' => fn($q) => $q->latest()])->get() 
            : collect();

        return view('user.dashboard', compact('popularBooks', 'newBooks', 'categories'));
    }

    public function transactions()
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $todayIncome = Transaction::whereDate('created_at', $today)->sum('price');
        $monthIncome = Transaction::whereMonth('created_at', $currentMonth)
                                  ->whereYear('created_at', $currentYear)
                                  ->sum('price');
        $totalIncome = Transaction::sum('price');

        $transactions = Transaction::latest()->get();

        return view('admin.transactions', compact('todayIncome', 'monthIncome', 'totalIncome', 'transactions'));
    }
}