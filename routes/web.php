<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes - Ruang Pustaka
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama (Admin langsung ke Dashboard, User/Tamu ke Welcome)
Route::get('/', function () {
    if (Auth::check() && Auth::user()->email === 'rafibadillah8@gmail.com') {
        $books = class_exists(Book::class) ? Book::latest()->paginate(10) : collect();
        return view('admin.dashboard', compact('books'));
    }

    $popularBooks = class_exists(Book::class) 
        ? Book::latest()->take(10)->get() 
        : collect();
    
    $newBooks = class_exists(Book::class) 
        ? Book::where('created_at', '>=', Carbon::now()->subHours(24))->latest()->get() 
        : collect();
        
    $categories = class_exists(Category::class) 
        ? Category::has('books')->with(['books' => fn($q) => $q->latest()])->get() 
        : collect();

    return view('welcome', compact('popularBooks', 'newBooks', 'categories'));
})->name('home');

// 2. Route Buku & Search (Hanya bisa diakses jika SUDAH login)
Route::get('/books', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $search = $request->query('search');
    $category = $request->query('category');

    $books = class_exists(Book::class) 
        ? Book::when($search, function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%");
        })->when($category, function($q) use ($category) {
            $q->where('category', 'like', "%{$category}%");
        })->latest()->get()
        : collect();

    $categories = class_exists(Category::class) ? Category::all() : collect();
    $viewName = view()->exists('user.books.index') ? 'user.books.index' : 'books.index';
    return view($viewName, compact('books', 'search', 'categories'));
})->name('books.index');
 
// Route Admin Transactions / Laporan Transaksi & Pendapatan
Route::get('/admin/transactions', function () {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $todayIncome = class_exists(Book::class) 
        ? Book::whereDate('created_at', Carbon::today())->sum('price') 
        : 0;

    $monthIncome = class_exists(Book::class) 
        ? Book::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('price') 
        : 0;

    $totalIncome = class_exists(Book::class) 
        ? Book::sum('price') 
        : 0;

    $transactions = class_exists(Book::class) ? Book::latest()->get() : collect();

    return view('admin.transactions', compact('todayIncome', 'monthIncome', 'totalIncome', 'transactions'));
})->name('admin.transactions.index');

// Route Tambah Buku (Form Create) - Hanya untuk Admin
Route::get('/books/create', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    if (Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $viewName = view()->exists('admin.books.create') ? 'admin.books.create' : 'books.create';
    $categories = class_exists(Category::class) ? Category::all() : collect();
    
    return view($viewName, compact('categories'));
})->name('books.create');

// Route Simpan Buku Baru (Store) - Hanya untuk Admin
Route::post('/books', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'publisher' => 'required|string|max:255', // Validasi publisher ditambahkan
        'price' => 'required|numeric',
        'category' => 'required|array', // Validasi kategori berupa array checkbox
    ]);

    // Sertakan 'publisher' ke dalam array data yang akan disimpan
    $data = $request->only(['title', 'author', 'publisher', 'price']);
    
    // Gabungkan array kategori checkbox menjadi string terpisah koma
    $data['category'] = implode(', ', $request->category);

    if ($request->hasFile('cover_image')) {
        $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
    }

    if ($request->hasFile('file_path')) {
        $data['file_path'] = $request->file('file_path')->store('books_pdf', 'public');
    }

    if (class_exists(Book::class)) {
        Book::create($data);
    }

    return redirect()->route('admin.dashboard')->with('success', 'Buku berhasil ditambahkan!');
})->name('books.store');

Route::get('/books/{id}', function ($id) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $book = class_exists(Book::class) ? Book::find($id) : null;
    $viewName = view()->exists('admin.books.show') ? 'admin.books.show' : 'books.show';
    return view($viewName, compact('book', 'id'));
})->name('books.show');

// Route Form Edit Buku - Hanya untuk Admin
Route::get('/books/{id}/edit', function ($id) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $book = class_exists(Book::class) ? Book::find($id) : null;
    $categories = class_exists(Category::class) ? Category::all() : collect();
    $viewName = view()->exists('admin.books.edit') ? 'admin.books.edit' : 'books.edit';
    
    return view($viewName, compact('book', 'categories'));
})->name('books.edit');

// Route Update / Simpan Perubahan Buku - Hanya untuk Admin
Route::put('/books/{id}', function (Request $request, $id) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'publisher' => 'required|string|max:255', // Validasi publisher saat edit
        'price' => 'required|numeric',
        'category' => 'required|array', // Validasi kategori checkbox saat edit
    ]);

    $book = Book::findOrFail($id);
    
    // Sertakan 'publisher' ke dalam data update
    $data = $request->only(['title', 'author', 'publisher', 'price']);
    
    // Ubah array checkbox kategori menjadi string koma agar tersimpan ke database
    $data['category'] = implode(', ', $request->category);

    if ($request->hasFile('cover_image')) {
        $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
    }

    if ($request->hasFile('file_path')) {
        $data['file_path'] = $request->file('file_path')->store('books_pdf', 'public');
    }

    $book->update($data);

    return redirect()->route('admin.dashboard')->with('success', 'Buku berhasil diperbarui!');
})->name('books.update');

// Route Hapus Buku - Hanya untuk Admin
Route::delete('/books/{id}', function ($id) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $book = Book::findOrFail($id);
    $book->delete();

    return redirect()->route('admin.dashboard')->with('success', 'Buku berhasil dihapus!');
})->name('books.destroy');

// 3. Admin Dashboard Route - Hanya untuk Admin
Route::get('/admin/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $books = class_exists(Book::class) ? Book::latest()->paginate(10) : collect();

    return view('admin.dashboard', compact('books'));
})->name('admin.dashboard');

// 4. Autentikasi LOGIN
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'Email atau password yang kamu masukkan tidak cocok.',
    ])->onlyInput('email');
});

// 5. Autentikasi REGISTER
Route::get('/register', function () {
    if (Auth::check()) {
        return redirect('/');
    }
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed'],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    Auth::login($user);

    return redirect('/');
});

// 6. Autentikasi LOGOUT
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');