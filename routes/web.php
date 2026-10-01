<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\PaymentOrder;
use Carbon\Carbon;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes - Ruang Pustaka
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama (Admin langsung ke Dashboard, User/Tamu ke Welcome)
Route::get('/', function () {
    if (Auth::check() && Auth::user()->email === 'rafibadillah8@gmail.com') {
        $books = class_exists(Book::class) ? Book::with('categories')->latest()->paginate(10) : collect();
        return view('admin.dashboard', compact('books'));
    }

    $popularBooks = class_exists(Book::class) ? Book::latest()->take(10)->get() : collect();
    $newBooks = class_exists(Book::class) ? Book::where('created_at', '>=', Carbon::now()->subHours(24))->latest()->get() : collect();
    $categories = class_exists(Category::class) ? Category::has('books')->with(['books' => fn($q) => $q->latest()])->get() : collect();

    // Ambil satu voucher yang masih aktif & valid dipakai untuk ditampilkan di banner
    $activeVoucher = class_exists(Voucher::class)
        ? Voucher::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->latest()
            ->first()
        : null;

    return view('welcome', compact('popularBooks', 'newBooks', 'categories', 'activeVoucher'));
})->name('home');

// 2. Autentikasi (Login, Register, Logout)
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

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// 2b. Profil User
Route::get('/profile', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return view('profile.edit', ['user' => Auth::user()]);
})->name('profile.edit');

Route::put('/profile', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'password' => ['nullable', 'confirmed', 'min:8'],
    ]);

    $user->name = $request->name;
    $user->email = $request->email;

    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }

    $user->save();

    return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
})->name('profile.update');

// 2c. Daftar Buku Saya (Buku yang sudah dibeli / diakses user)
Route::get('/my-books', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $bookIds = Transaction::where('user_id', Auth::id())
        ->whereNotNull('book_id')
        ->pluck('book_id')
        ->merge(
            PaymentOrder::where('user_id', Auth::id())
                ->where('status', 'paid')
                ->pluck('book_id')
        )
        ->unique()
        ->values();

    $books = class_exists(Book::class)
        ? Book::whereIn('id', $bookIds)->latest()->get()
        : collect();

    return view('user.bukusaya', compact('books'));
})->name('books.myBooks');

// Wishlist
Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->middleware('auth')->name('wishlist.index');
Route::post('/wishlist/{book}', [\App\Http\Controllers\WishlistController::class, 'toggle'])->middleware('auth')->name('wishlist.toggle');

// Reviews
Route::post('/books/{book}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->middleware('auth')->name('reviews.store');
Route::delete('/reviews/{id}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->middleware('auth')->name('reviews.destroy');

// 3. Admin Dashboard & Manajemen Buku
Route::get('/admin/dashboard', function () {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }
    $books = class_exists(Book::class) ? Book::with('categories')->latest()->paginate(10) : collect();
    return view('admin.dashboard', compact('books'));
})->middleware(['auth', 'admin'])->name('admin.dashboard');

Route::get('/admin/transactions', function () {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $todayIncome = class_exists(Transaction::class) ? Transaction::whereDate('created_at', Carbon::today())->sum('price') : 0;
    $monthIncome = class_exists(Transaction::class) ? Transaction::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('price') : 0;
    $totalIncome = class_exists(Transaction::class) ? Transaction::sum('price') : 0;
    $transactions = class_exists(Transaction::class) ? Transaction::latest()->get() : collect();

    return view('admin.transactions', compact('todayIncome', 'monthIncome', 'totalIncome', 'transactions'));
})->middleware(['auth', 'admin'])->name('admin.transactions.index');

Route::get('/admin/transactions/export', function () {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }
    return app(\App\Http\Controllers\TransactionExportController::class)->exportPdf();
})->middleware(['auth', 'admin'])->name('admin.transactions.export');

// Rute Admin Grup (Kategori, JSON Data Buku, dan CRUD Buku Admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');

    Route::post('/categories/ajax', [CategoryController::class, 'storeAjax'])->name('categories.store.ajax');
    Route::delete('/categories/{id}/ajax', [CategoryController::class, 'destroyAjax'])->name('categories.destroy.ajax');
    Route::get('/books/{id}/json', [BookController::class, 'getJsonData'])->name('books.json');
});

// 3b. Admin - Manajemen Voucher
Route::get('/admin/vouchers', function () {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }
    $vouchers = class_exists(Voucher::class) ? Voucher::latest()->paginate(10) : collect();
    return view('admin.vouchers.index', compact('vouchers'));
})->middleware(['auth', 'admin'])->name('admin.vouchers.index');

Route::get('/admin/vouchers/create', function () {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }
    $books = class_exists(Book::class) ? Book::all() : collect();
    return view('admin.vouchers.create', compact('books'));
})->middleware(['auth', 'admin'])->name('admin.vouchers.create');

Route::post('/admin/vouchers', function (Request $request) {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $request->validate([
        'code' => 'required|string|max:50|unique:vouchers,code',
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric|min:0',
        'scope' => 'required|in:all,specific',
        'books' => 'nullable|array',
        'usage_limit' => 'nullable|integer|min:1',
        'expires_at' => 'nullable|date',
    ]);

    $data = $request->except(['books']);
    $data['code'] = strtoupper($request->code);
    $data['is_active'] = true;
    $data['used_count'] = 0;

    $voucher = Voucher::create($data);

    if ($request->scope === 'specific' && $request->has('books')) {
        $voucher->books()->sync($request->books);
    }

    return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dibuat!');
})->middleware(['auth', 'admin'])->name('admin.vouchers.store');

Route::get('/admin/vouchers/{id}/edit', function ($id) {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }
    $voucher = Voucher::with('books')->findOrFail($id);
    $books = class_exists(Book::class) ? Book::all() : collect();
    return view('admin.vouchers.edit', compact('voucher', 'books'));
})->middleware(['auth', 'admin'])->name('admin.vouchers.edit');

Route::put('/admin/vouchers/{id}', function (Request $request, $id) {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }

    $voucher = Voucher::findOrFail($id);

    $request->validate([
        'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric|min:0',
        'scope' => 'required|in:all,specific',
        'books' => 'nullable|array',
        'usage_limit' => 'nullable|integer|min:1',
        'expires_at' => 'nullable|date',
    ]);

    $data = $request->except(['books']);
    $data['code'] = strtoupper($request->code);
    $data['is_active'] = $request->boolean('is_active');

    $voucher->update($data);

    if ($request->scope === 'specific') {
        $voucher->books()->sync($request->books ?? []);
    } else {
        $voucher->books()->detach();
    }

    return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diperbarui!');
})->middleware(['auth', 'admin'])->name('admin.vouchers.update');

Route::delete('/admin/vouchers/{id}', function ($id) {
    if (!Auth::check() || Auth::user()->email !== 'rafibadillah8@gmail.com') {
        return redirect()->route('home');
    }
    $voucher = Voucher::findOrFail($id);
    $voucher->books()->detach();
    $voucher->delete();
    return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
})->middleware(['auth', 'admin'])->name('admin.vouchers.destroy');


// 4. Katalog Buku & Interaksi User (Reader & Transaksi)
Route::get('/books', [BookController::class, 'index'])->name('books.index');

// Rute JSON untuk autocomplete search bar di navbar
Route::get('/books/search-suggestions', [BookController::class, 'searchSuggestions'])->name('books.searchSuggestions');

// Rute ambil Snap Token Midtrans (dipanggil AJAX sebelum popup pembayaran muncul)
Route::post('/books/{id}/snap-token', [PaymentController::class, 'getSnapToken'])->middleware('auth')->name('payment.snapToken');

// Rute callback dari Midtrans setelah pembayaran selesai (finish/unfinish/error)
Route::get('/books/{id}/payment-callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');

// Rute parameter dinamis diletakkan di bawah rute statis seperti /books/search-suggestions
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

// Rute Proses Pembelian / Akses Instan Buku
Route::post('/books/{id}/buy', [BookController::class, 'processBuy'])->middleware('auth')->name('books.processBuy');

// Rute cek status pembayaran ke Midtrans (untuk pembayaran yang lunas setelah popup ditutup)
Route::post('/books/{id}/check-payment', [BookController::class, 'checkPayment'])->middleware('auth')->name('books.checkPayment');

// SEMENTARA - halaman diagnosa pembayaran. Hapus route ini kalau masalah sudah selesai.
// Hanya aktif di environment "local" dan untuk user yang sedang login.
// Rute Membaca E-Book Langsung di Website (Protected Reader)
Route::get('/books/{id}/read', [UserController::class, 'readBook'])->name('books.read');

// Rute Apply Kode Voucher (dipakai user di halaman detail buku)
Route::post('/voucher/apply', [VoucherController::class, 'apply'])->name('voucher.apply');