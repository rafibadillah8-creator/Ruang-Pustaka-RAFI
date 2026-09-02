<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen pb-16">

    <!-- Navbar Dynamic (Tamu vs Auth) -->
    <nav class="border-b border-slate-200 bg-white/80 backdrop-blur px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="{{ url('/') }}" class="text-xl font-bold text-[#2563EB]">Ruang Pustaka</a>
        
        <div class="flex items-center gap-3">
            <!-- Tombol Search (Tamu -> Login, User Auth -> Halaman Buku) -->
            <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="p-2 text-slate-500 hover:text-[#2563EB] hover:bg-slate-100 rounded-lg transition" title="Cari Buku">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </a>

            @guest
                <!-- Jika Tamu (Belum Login) -->
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-[#2563EB] px-3 py-1.5 transition">Log in</a>
                <a href="{{ route('register') }}" class="text-sm font-semibold bg-[#2563EB] hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg transition shadow-sm">Register</a>
            @endguest

            @auth
                <!-- Jika Sudah Login -->
                <span class="text-sm font-medium text-slate-700">Halo, <b>{{ Auth::user()->name }}</b></span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 pt-6 space-y-10">

        <!-- Banner Informasi -->
        <div class="bg-[#2563EB] text-white p-6 rounded-2xl flex justify-between items-center shadow-lg">
            <div>
                <p class="text-sm font-medium">E-book yang dibeli hanya diakses di platform ini, tidak tersedia dalam format PDF.</p>
            </div>
        </div>

        <!-- ==================== 1. BUKU POPULER ==================== -->
        <section class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-[#0F172A]">Buku Populer</h2>
                
                <div class="flex items-center gap-3">
                    <!-- Tombol Tambah Buku Khusus Admin -->
                    @auth
                        @if(strtolower(Auth::user()->role ?? '') === 'admin' || (isset(Auth::user()->is_admin) && Auth::user()->is_admin))
                            <a href="{{ route('books.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Buku
                            </a>
                        @endif
                    @endauth

                    <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="text-xs md:text-sm font-semibold text-[#2563EB] hover:underline flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="relative group">
                @if(count($popularBooks ?? []) > 4)
                    <button onclick="scrollSlider('slider-popular', 'left')" class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white border border-slate-200 shadow-md p-2 rounded-full hover:bg-slate-50 text-slate-600 transition hidden group-hover:block">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button onclick="scrollSlider('slider-popular', 'right')" class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-white border border-slate-200 shadow-md p-2 rounded-full hover:bg-slate-50 text-slate-600 transition hidden group-hover:block">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @endif

                <div id="slider-popular" class="flex gap-4 overflow-x-auto scroll-smooth no-scrollbar py-2">
                    @forelse($popularBooks ?? [] as $book)
                        @php
                            $targetUrl = Auth::check() ? route('books.show', data_get($book, 'id', 1)) : route('login');
                        @endphp
                        <div class="w-[170px] sm:w-[190px] shrink-0 bg-white border border-slate-200 rounded-2xl p-3 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <!-- Klik Gambar -->
                                <a href="{{ $targetUrl }}" class="block w-full h-52 bg-slate-100 rounded-xl overflow-hidden mb-3">
                                    @if(data_get($book, 'cover_image'))
                                        <img src="{{ asset('storage/' . data_get($book, 'cover_image')) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2zm-3 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-6 6h6v1H9v-1z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                                <!-- Klik Judul -->
                                <a href="{{ $targetUrl }}" class="font-bold text-sm text-slate-900 truncate mb-1 block hover:text-[#2563EB]" title="{{ data_get($book, 'title') }}">
                                    {{ data_get($book, 'title', 'Judul Buku') }}
                                </a>
                                <p class="text-xs text-slate-500 flex items-center gap-1 mb-2 truncate">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ data_get($book, 'author', 'Author') }}</span>
                                </p>
                            </div>
                            <div>
                                <hr class="border-slate-200 mb-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs sm:text-sm text-slate-900">
                                        Rp {{ number_format(data_get($book, 'price', 0), 0, ',', '.') }}
                                    </span>
                                    <!-- Klik Keranjang -->
                                    <a href="{{ $targetUrl }}" class="p-1.5 border border-slate-200 hover:border-blue-500 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition" title="Beli Buku">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-slate-400 text-sm py-4">Belum ada buku populer.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ==================== 2. BUKU BARU (< 24 JAM) ==================== -->
        @if(count($newBooks ?? []) > 0)
        <section class="space-y-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-[#0F172A]">Buku Baru</h2>
                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">&lt; 24 Jam</span>
                </div>
                <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="text-xs md:text-sm font-semibold text-[#2563EB] hover:underline flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="relative group">
                @if(count($newBooks) > 4)
                    <button onclick="scrollSlider('slider-new', 'left')" class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white border border-slate-200 shadow-md p-2 rounded-full hover:bg-slate-50 text-slate-600 transition hidden group-hover:block">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button onclick="scrollSlider('slider-new', 'right')" class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-white border border-slate-200 shadow-md p-2 rounded-full hover:bg-slate-50 text-slate-600 transition hidden group-hover:block">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @endif

                <div id="slider-new" class="flex gap-4 overflow-x-auto scroll-smooth no-scrollbar py-2">
                    @foreach($newBooks as $book)
                        @php
                            $targetUrl = Auth::check() ? route('books.show', data_get($book, 'id', 1)) : route('login');
                        @endphp
                        <div class="w-[170px] sm:w-[190px] shrink-0 bg-white border border-slate-200 rounded-2xl p-3 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <a href="{{ $targetUrl }}" class="block w-full h-52 bg-slate-100 rounded-xl overflow-hidden mb-3">
                                    @if(data_get($book, 'cover_image'))
                                        <img src="{{ asset('storage/' . data_get($book, 'cover_image')) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2zm-3 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-6 6h6v1H9v-1z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                                <a href="{{ $targetUrl }}" class="font-bold text-sm text-slate-900 truncate mb-1 block hover:text-[#2563EB]" title="{{ data_get($book, 'title') }}">
                                    {{ data_get($book, 'title', 'Judul Buku') }}
                                </a>
                                <p class="text-xs text-slate-500 flex items-center gap-1 mb-2 truncate">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ data_get($book, 'author', 'Author') }}</span>
                                </p>
                            </div>
                            <div>
                                <hr class="border-slate-200 mb-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs sm:text-sm text-slate-900">
                                        Rp {{ number_format(data_get($book, 'price', 0), 0, ',', '.') }}
                                    </span>
                                    <a href="{{ $targetUrl }}" class="p-1.5 border border-slate-200 hover:border-blue-500 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition" title="Beli Buku">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- ==================== 3. KATEGORI BUKU ==================== -->
        @foreach($categories ?? [] as $category)
            @if(count(data_get($category, 'books', [])) > 0)
            <section class="space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-[#0F172A]">{{ data_get($category, 'name', 'Kategori') }}</h2>
                    <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="text-xs md:text-sm font-semibold text-[#2563EB] hover:underline flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="relative group">
                    @if(count(data_get($category, 'books', [])) > 4)
                        <button onclick="scrollSlider('slider-cat-{{ data_get($category, 'id') }}', 'left')" class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white border border-slate-200 shadow-md p-2 rounded-full hover:bg-slate-50 text-slate-600 transition hidden group-hover:block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button onclick="scrollSlider('slider-cat-{{ data_get($category, 'id') }}', 'right')" class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-white border border-slate-200 shadow-md p-2 rounded-full hover:bg-slate-50 text-slate-600 transition hidden group-hover:block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif

                    <div id="slider-cat-{{ data_get($category, 'id') }}" class="flex gap-4 overflow-x-auto scroll-smooth no-scrollbar py-2">
                        @foreach(data_get($category, 'books', []) as $book)
                            @php
                                $targetUrl = Auth::check() ? route('books.show', data_get($book, 'id', 1)) : route('login');
                            @endphp
                            <div class="w-[170px] sm:w-[190px] shrink-0 bg-white border border-slate-200 rounded-2xl p-3 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                                <div>
                                    <a href="{{ $targetUrl }}" class="block w-full h-52 bg-slate-100 rounded-xl overflow-hidden mb-3">
                                        @if(data_get($book, 'cover_image'))
                                            <img src="{{ asset('storage/' . data_get($book, 'cover_image')) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-300">
                                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2zm-3 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-6 6h6v1H9v-1z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </a>
                                    <a href="{{ $targetUrl }}" class="font-bold text-sm text-slate-900 truncate mb-1 block hover:text-[#2563EB]" title="{{ data_get($book, 'title') }}">
                                        {{ data_get($book, 'title', 'Judul Buku') }}
                                    </a>
                                    <p class="text-xs text-slate-500 flex items-center gap-1 mb-2 truncate">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>{{ data_get($book, 'author', 'Author') }}</span>
                                    </p>
                                </div>
                                <div>
                                    <hr class="border-slate-200 mb-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-xs sm:text-sm text-slate-900">
                                            Rp {{ number_format(data_get($book, 'price', 0), 0, ',', '.') }}
                                        </span>
                                        <a href="{{ $targetUrl }}" class="p-1.5 border border-slate-200 hover:border-blue-500 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition" title="Beli Buku">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif
        @endforeach

    </main>

    <!-- Script Slider Horisontal -->
    <script>
        function scrollSlider(elementId, direction) {
            const container = document.getElementById(elementId);
            if (!container) return;
            const scrollAmount = 300;
            if (direction === 'left') {
                container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }
    </script>
</body>
</html>