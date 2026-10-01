<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog & Jelajah Buku - Ruang Pustaka</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
</head>
<body class="bg-parchment text-ink-900 font-sans min-h-screen flex flex-col relative pb-28 antialiased">

    <!-- Header / Navbar -->
    <nav class="border-b border-wine-900 bg-gradient-to-r from-wine-900 via-ink-900 to-wine-900 px-6 py-4 flex justify-between items-center sticky top-0 z-40">
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}" title="Kembali ke Beranda" class="p-2 text-wine-200 hover:text-gold-400 hover:bg-wine-800/60 rounded-full transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>

            <a href="{{ url('/') }}" class="text-xl font-bold text-gold-400 hover:text-gold-500 flex items-center gap-2.5 transition">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka" class="h-9 w-9 object-contain">
                <span class="font-serif">Ruang Pustaka</span>
            </a>
        </div>

        <div class="flex items-center gap-4">
            @if(Auth::check())
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-2 text-sm text-wine-100 hover:text-gold-400 px-3 py-1.5 rounded-full hover:bg-wine-800/60 transition">
                        <span class="hidden md:inline">Selamat Datang, <strong class="text-gold-400">{{ Auth::user()->name ?? 'User' }}</strong></span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-cloak x-show="open" x-transition
                         class="absolute right-0 mt-2 w-56 bg-ink-900 border border-wine-800 rounded-2xl shadow-xl py-2 z-50">

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil
                        </a>

                        <a href="{{ route('books.myBooks') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Daftar Buku Saya
                        </a>

                        <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition">
                            <svg class="w-4 h-4 shrink-0 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Wishlist
                        </a>

                        <hr class="border-wine-800 my-1">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-red-300 hover:text-red-100 hover:bg-red-900/40 transition">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="text-xs bg-gold-500 text-ink-900 px-4 py-2 rounded-full font-bold shadow-md hover:bg-gold-400 transition">
                        Masuk
                    </a>
                </div>
            @endif
        </div>
    </nav>

    <!-- Content Utama -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-6 pt-6 space-y-8">

        <!-- Hero Banner Digital Library -->
        <section class="bg-gradient-to-br from-wine-900 via-ink-900 to-wine-950 rounded-3xl p-8 md:p-12 text-wine-100 flex flex-col md:flex-row items-center justify-between gap-8 shadow-2xl relative overflow-hidden border border-wine-800">
            <div class="space-y-4 max-w-xl z-10">
                <span class="bg-gold-500/20 text-gold-400 text-xs font-bold uppercase tracking-widest px-3.5 py-1.5 rounded-full border border-gold-500/30 inline-block">
                    Temukan Bacaan Favoritmu
                </span>
                <h1 class="font-serif text-3xl md:text-5xl font-bold text-white leading-tight">
                    Find the book you like the most
                </h1>
                <p class="text-wine-200 text-sm leading-relaxed">
                    Jelajahi koleksi komik, novel, sejarah, dan arsip digital. Cari berdasarkan judul, penulis, atau gunakan filter kategori.
                </p>
            </div>

            <div class="hidden lg:flex items-center gap-4 bg-ink-900/60 backdrop-blur-md border border-wine-800/80 p-5 rounded-2xl z-10">
                <!-- [GEMINI] FITUR KOLEKSI BUKU AKURAT -->
                <!-- Menampilkan total semua buku di database, bukan hanya hasil filter -->
                <div class="text-center px-3">
                    <span class="block text-2xl font-bold text-gold-400">{{ \App\Models\Book::count() }}+</span>
                    <span class="text-[11px] text-wine-200 uppercase tracking-wider">Koleksi</span>
                </div>
                <div class="h-10 w-px bg-wine-800"></div>
                <!-- [GEMINI] FITUR RATING BUKU AKURAT -->
                <!-- Mengambil rata-rata rating dari tabel reviews, default ke 4.8 jika belum ada -->
                <div class="text-center px-3">
                    <span class="block text-2xl font-bold text-emerald-400">{{ number_format(\App\Models\Review::avg('rating') ?? 4.8, 1) }}</span>
                    <span class="text-[11px] text-wine-200 uppercase tracking-wider">Rating</span>
                </div>
            </div>
        </section>

        <!-- Form Pencarian & Filter Bar -->
        <form id="searchForm" method="GET" action="{{ route('books.index') }}" class="flex flex-col md:flex-row gap-3 items-center">
            @if(request('category_id'))
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <!-- Kotak Input Pencarian -->
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, deskripsi..." class="w-full pl-10 pr-4 py-3 bg-white border border-stone-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-wine-600 text-xs text-stone-800 placeholder-stone-400 shadow-sm transition">
                <svg class="w-4 h-4 text-wine-600 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Tombol Filter Modal -->
            <button type="button" onclick="openFilterModal()" class="w-full md:w-auto bg-white border border-stone-300 text-stone-800 px-4 py-3 rounded-2xl font-semibold text-xs hover:bg-stone-50 transition flex items-center justify-center gap-2 shadow-sm cursor-pointer shrink-0 relative">
                <svg class="w-4 h-4 text-wine-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span>Filter</span>
                @if(request('category_id') || (request('sort') && request('sort') !== 'latest'))
                    <span class="w-2 h-2 bg-wine-700 rounded-full"></span>
                @endif
            </button>

            <!-- Tombol Cari -->
            <button type="submit" class="w-full md:w-auto bg-wine-700 hover:bg-wine-800 text-white px-6 py-3 rounded-2xl font-semibold text-xs transition flex items-center justify-center gap-2 shadow-sm cursor-pointer shrink-0">
                Cari
            </button>
        </form>

        <div class="flex justify-between items-center pt-2 border-t border-wine-200/50">
            <h2 class="text-xs font-bold text-wine-900/70 uppercase tracking-wider">
                Semua Buku <span class="text-wine-900 font-normal">({{ method_exists($books, 'total') ? $books->total() : count($books) }} buku ditemukan)</span>
            </h2>
            
            @if(request()->hasAny(['search', 'category_id', 'sort']))
                <a href="{{ route('books.index') }}" class="text-xs text-wine-700 hover:underline font-semibold">
                    Hapus Filter &times;
                </a>
            @endif
        </div>

        <!-- Grid Buku -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
            @forelse($books as $book)
                <div class="bg-wine-100 border border-wine-300 rounded-3xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <a href="{{ route('books.show', $book->id) }}" class="w-full h-48 bg-stone-50 rounded-2xl overflow-hidden mb-3 border border-ink-900/15 flex items-center justify-center group relative">
                            @if(!empty($book->cover_image))
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="flex flex-col items-center justify-center text-stone-400">
                                    <svg class="w-10 h-10 text-stone-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="text-[10px] font-medium">No Cover</span>
                                </div>
                            @endif
                            <!-- [GEMINI] FITUR RATING BUKU INDIVIDUAL AKURAT -->
                            <!-- Mengambil rata-rata rating spesifik untuk buku ini -->
                            <div class="absolute top-2.5 right-2.5 bg-ink-900/75 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-lg flex items-center gap-1">
                                <span class="text-gold-400">&#9733;</span> {{ number_format($book->reviews()->avg('rating') ?? 4.8, 1) }}
                            </div>
                        </a>

                        <a href="{{ route('books.show', $book->id) }}" class="font-bold text-stone-900 text-xs line-clamp-1 hover:text-wine-700 transition" title="{{ $book->title }}">
                            {{ $book->title }}
                        </a>

                        <p class="text-[11px] text-stone-500 mt-0.5 truncate">
                            {{ $book->author ?? 'Penulis Tidak Diketahui' }}
                        </p>

                        <!-- Bagian Kategori -->
                        <div class="flex flex-wrap gap-1 mt-2">
                            @if(isset($book->categories) && count($book->categories) > 0)
                                @foreach($book->categories as $cat)
                                    <span class="inline-block text-[10px] bg-wine-50 text-wine-700 px-2 py-0.5 rounded-md font-semibold border border-wine-100">
                                        {{ $cat->name }}
                                    </span>
                                @endforeach
                            @elseif($book->category)
                                <span class="inline-block text-[10px] bg-wine-50 text-wine-700 px-2 py-0.5 rounded-md font-semibold border border-wine-100">
                                    {{ is_object($book->category) ? $book->category->name : $book->category }}
                                </span>
                            @else
                                <span class="inline-block text-[10px] bg-stone-100 text-stone-500 px-2 py-0.5 rounded-md font-semibold">
                                    Tanpa Kategori
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-wine-200/40 flex items-center justify-between">
                        <span class="text-xs font-bold text-stone-900">
                            {{ isset($book->price) && $book->price > 0 ? 'Rp ' . number_format($book->price, 0, ',', '.') : 'Gratis' }}
                        </span>
                        
                        <a href="{{ route('books.show', $book->id) }}" class="p-2 border border-wine-200 rounded-xl hover:bg-wine-50 transition text-wine-700" title="Detail Buku">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-stone-400 text-xs bg-white rounded-3xl border border-stone-200">
                    Buku tidak ditemukan untuk kriteria ini.
                </div>
            @endforelse
        </div>

        @if(method_exists($books, 'links'))
            <div class="mt-6">
                {{ $books->appends(request()->query())->links() }}
            </div>
        @endif
    </main>

    <!-- MODAL FILTER -->
    <div id="filterModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-stone-900/40 backdrop-blur-xs transition-opacity">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in zoom-in-95 duration-200">
            <form method="GET" action="{{ route('books.index') }}" class="flex flex-col h-full overflow-hidden">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="p-5 border-b border-stone-100 flex items-center justify-between shrink-0">
                    <h3 class="text-base font-bold text-stone-900">Filter Pencarian</h3>
                    <button type="button" onclick="closeFilterModal()" class="text-stone-400 hover:text-stone-600 p-1 rounded-full hover:bg-stone-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-5 py-2.5 bg-stone-50/80 border-b border-stone-100 flex items-center justify-between text-xs shrink-0">
                    <span class="text-stone-500 font-medium">
                        {{ method_exists($books, 'total') ? $books->total() : count($books) }} buku ditemukan
                    </span>
                    <a href="{{ route('books.index') }}" class="text-wine-700 font-semibold hover:underline">
                        Hapus Semua
                    </a>
                </div>

                <div class="p-5 overflow-y-auto space-y-6 flex-1 text-xs">
                    @if(isset($categories) && count($categories) > 0)
                        <div>
                            <h4 class="font-bold text-stone-400 mb-3 uppercase tracking-wider text-[11px]">Kategori Buku</h4>
                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-stone-50 cursor-pointer transition">
                                    <input type="radio" name="category_id" value="" {{ !request('category_id') ? 'checked' : '' }} class="w-4 h-4 text-wine-700 border-stone-300 focus:ring-wine-500">
                                    <span class="font-medium text-stone-700">Semua Kategori</span>
                                </label>
                                @foreach($categories as $cat)
                                    <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-stone-50 cursor-pointer transition">
                                        <input type="radio" name="category_id" value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'checked' : '' }} class="w-4 h-4 text-wine-700 border-stone-300 focus:ring-wine-500">
                                        <span class="font-medium text-stone-700">{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <hr class="border-stone-100">
                    @endif

                    <div>
                        <h4 class="font-bold text-stone-400 mb-3 uppercase tracking-wider text-[11px]">Urutkan Berdasarkan</h4>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-stone-50 cursor-pointer transition">
                                <input type="radio" name="sort" value="latest" {{ request('sort', 'latest') == 'latest' ? 'checked' : '' }} class="w-4 h-4 text-wine-700 border-stone-300 focus:ring-wine-500">
                                <span class="font-medium text-stone-700">Buku Terbaru</span>
                            </label>
                            <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-stone-50 cursor-pointer transition">
                                <input type="radio" name="sort" value="popular" {{ request('sort') == 'popular' ? 'checked' : '' }} class="w-4 h-4 text-wine-700 border-stone-300 focus:ring-wine-500">
                                <span class="font-medium text-stone-700">Paling Populer</span>
                            </label>
                            <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-stone-50 cursor-pointer transition">
                                <input type="radio" name="sort" value="harga_tertinggi" {{ request('sort') == 'harga_tertinggi' ? 'checked' : '' }} class="w-4 h-4 text-wine-700 border-stone-300 focus:ring-wine-500">
                                <span class="font-medium text-stone-700">Harga: Termahal &rarr; Termurah</span>
                            </label>
                            <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-stone-50 cursor-pointer transition">
                                <input type="radio" name="sort" value="harga_terendah" {{ request('sort') == 'harga_terendah' ? 'checked' : '' }} class="w-4 h-4 text-wine-700 border-stone-300 focus:ring-wine-500">
                                <span class="font-medium text-stone-700">Harga: Termurah &rarr; Termahal</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-stone-100 bg-stone-50/50 shrink-0">
                    <button type="submit" onclick="closeFilterModal()" class="w-full bg-wine-700 hover:bg-wine-800 text-white py-3 rounded-2xl font-bold text-xs shadow-md transition cursor-pointer">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Kontrol Modal -->
    <script>
        function openFilterModal() {
            const modal = document.getElementById('filterModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeFilterModal() {
            const modal = document.getElementById('filterModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const filterModal = document.getElementById('filterModal');
            if (filterModal) {
                filterModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeFilterModal();
                    }
                });
            }
        });
    </script>
</body>
</html>