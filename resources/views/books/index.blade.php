<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen pb-16">

    <!-- Header / Navbar -->
    <nav class="border-b border-slate-200 bg-white px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="{{ url('/') }}" class="text-xl font-bold text-[#2563EB] flex items-center gap-2">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Ruang Pustaka
        </a>

        <div class="flex items-center gap-4">
            <span class="text-sm text-[#64748B] hidden md:inline">Selamat membaca, <strong class="text-[#0F172A]">{{ Auth::user()->name ?? 'User' }}</strong></span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 px-3 py-1.5 rounded-lg font-semibold transition">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 pt-6 space-y-6">

        <!-- Form Pencarian, Filter Kategori & Tombol Kembali ke Panel Admin (Admin) -->
        <form method="GET" action="{{ route('books.index') }}" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-3">
                <!-- Input Search Bar -->
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari buku, author, kategori..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#2563EB] text-sm transition">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Dropdown Filter Kategori -->
                @if(isset($categories) && count($categories) > 0)
                <select name="category_id" onchange="this.form.submit()" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#2563EB] transition">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @endif

                <!-- Tombol Submit Search/Filter -->
                <button type="submit" class="bg-[#2563EB] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>

                <!-- Tombol Kembali ke Panel Admin (Muncul Khusus Admin) -->
                @if(Auth::check() && (strtolower(Auth::user()->role ?? '') === 'admin' || Auth::user()->usertype === 'admin' || Auth::user()->is_admin))
                    <a href="{{ route('admin.dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 px-5 py-2.5 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Panel Admin
                    </a>
                @endif
            </div>

            <!-- Header Jumlah Buku & Tab Urutan -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pt-2 border-t border-slate-200">
                <h2 class="text-base font-bold text-[#0F172A]">
                    Semua Buku <span class="text-slate-500 font-normal">({{ method_exists($books, 'total') ? $books->total() : count($books) }} buku)</span>
                </h2>

                <div class="flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" class="px-4 py-1.5 rounded-lg text-xs font-semibold {{ request('sort', 'latest') == 'latest' ? 'bg-white border border-slate-300 shadow-sm text-[#0F172A]' : 'text-slate-500 hover:text-slate-800' }}">
                        Terbaru
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" class="px-4 py-1.5 rounded-lg text-xs font-semibold {{ request('sort') == 'popular' ? 'bg-white border border-slate-300 shadow-sm text-[#0F172A]' : 'text-slate-500 hover:text-slate-800' }}">
                        Terpopuler
                    </a>
                </div>
            </div>
        </form>

        <!-- Listing Buku -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            @forelse($books as $book)
                <div class="bg-white border border-slate-200 rounded-2xl p-3.5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <a href="{{ route('books.show', $book->id) }}" class="block w-full h-48 bg-slate-100 rounded-xl overflow-hidden mb-3 border border-slate-100 flex items-center justify-center">
                            @if(!empty($book->cover_image))
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex flex-col items-center justify-center text-[#64748B]">
                                    <svg class="w-10 h-10 text-slate-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="text-[11px] font-medium">No Cover</span>
                                </div>
                            @endif
                        </a>

                        <a href="{{ route('books.show', $book->id) }}" class="font-bold text-[#0F172A] text-sm line-clamp-1 hover:text-[#2563EB] transition" title="{{ $book->title }}">
                            {{ $book->title }}
                        </a>

                        <p class="text-xs text-[#64748B] mt-1 truncate">
                            {{ $book->author ?? 'Penulis Tidak Diketahui' }}
                        </p>

                        <!-- Diperbaiki menggunakan ?->name untuk menghindari cetak JSON mentah -->
                        <div>
                            <span class="inline-block text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md mt-1.5 font-semibold">
                                {{ $book->category?->name ?? 'Tanpa Kategori' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <hr class="border-t border-slate-200 mb-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-[#0F172A]">
                                {{ isset($book->price) && $book->price > 0 ? 'Rp ' . number_format($book->price, 0, ',', '.') : 'Gratis' }}
                            </span>
                            
                            <div class="flex items-center gap-1.5">
                                <!-- Tombol Detail -->
                                <a href="{{ route('books.show', $book->id) }}" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-600 hover:text-[#2563EB]" title="Detail Buku">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <!-- Tombol Khusus Admin (Edit & Hapus) -->
                                @if(Auth::check() && (strtolower(Auth::user()->role ?? '') === 'admin' || Auth::user()->usertype === 'admin' || Auth::user()->is_admin))
                                    <!-- Edit -->
                                    <a href="{{ route('books.edit', $book->id) }}" class="p-2 border border-amber-200 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white rounded-lg transition" title="Edit Buku">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Hapus -->
                                    <form action="{{ route('books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus buku ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 border border-red-200 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition" title="Hapus Buku">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-slate-400">
                    Buku tidak ditemukan.
                </div>
            @endforelse
        </div>

        <!-- Links Pagination -->
        @if(method_exists($books, 'links'))
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @endif

    </main>
</body>
</html>