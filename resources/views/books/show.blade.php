<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $book->title }} - Ruang Pustaka</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Midtrans Snap JS Script -->
    <script type="text/javascript"
            src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
</head>
<body class="bg-parchment text-ink-900 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-gold-500 selection:text-ink-900">

    <div class="flex-1">
        <!-- Navbar Dynamic (Tamu vs Auth) -->
        <nav class="border-b border-wine-900/20 bg-gradient-to-r from-wine-900 via-ink-900 to-wine-900 backdrop-blur-md px-4 sm:px-8 py-3.5 flex justify-between items-center sticky top-0 z-50 transition-all shadow-md">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-lg sm:text-xl font-bold text-gold-400 hover:text-gold-300 transition">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka" class="h-10 w-10 object-contain drop-shadow">
                <span class="font-serif tracking-tight">Ruang Pustaka</span>
            </a>

            <!-- Search Bar dengan Autocomplete (desktop) -->
            <div class="hidden md:block flex-1 max-w-lg mx-6" x-data="bookSearch()">
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        type="text"
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @focus="open = query.length > 0"
                        @click.outside="open = false"
                        placeholder="Cari judul, penulis, atau kategori..."
                        class="w-full bg-white/95 border border-wine-800/20 focus:bg-white focus:border-gold-400 rounded-2xl pl-10 pr-4 py-2.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-gold-400/30 transition shadow-inner"
                    >
                    <div x-cloak x-show="open" x-transition
                         class="absolute mt-2 w-full bg-white rounded-2xl shadow-2xl border border-stone-200 overflow-hidden z-50">
                        <template x-if="loading">
                            <p class="px-4 py-3 text-xs text-stone-400">Mencari...</p>
                        </template>
                        <template x-if="!loading && results.length === 0">
                            <p class="px-4 py-3 text-xs text-stone-400">Tidak ada hasil untuk "<span x-text="query" class="font-semibold text-stone-600"></span>"</p>
                        </template>
                        <template x-for="r in results" :key="r.id">
                            <a :href="r.url"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-wine-50/60 transition-colors border-b border-stone-100 last:border-none">
                                <img :src="r.cover" :alt="r.title" class="h-11 w-8 rounded-lg object-cover shrink-0 bg-stone-100 border border-stone-200 shadow-xs">
                                <span class="text-xs min-w-0">
                                    <span class="block font-bold text-stone-900 truncate" x-text="r.title"></span>
                                    <span class="block text-[11px] text-stone-500 truncate mt-0.5" x-text="r.author"></span>
                                </span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Tombol Search (mobile) -->
                <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="md:hidden p-2 text-wine-200 hover:text-gold-400 hover:bg-wine-800/60 rounded-xl transition" title="Cari Buku">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </a>

                @guest
                    <!-- Jika Tamu (Belum Login) -->
                    <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold text-wine-100 hover:text-gold-400 px-3.5 py-2 transition">Log in</a>
                    <a href="{{ route('register') }}" class="text-xs sm:text-sm font-bold bg-gold-500 hover:bg-gold-400 text-ink-900 px-4 py-2 rounded-xl transition shadow-md hover:shadow-lg">Register</a>
                @endguest

                @auth
                    <!-- Jika Sudah Login -->
                    <div class="flex items-center pl-2 border-l border-wine-800/50" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 text-xs sm:text-sm font-medium text-wine-100 hover:text-gold-400 px-3 py-2 rounded-xl hover:bg-wine-800/60 transition">
                            <span class="hidden sm:inline">Halo, <b class="text-gold-400">{{ Auth::user()->name }}</b></span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-cloak x-show="open" x-transition
                             class="absolute right-4 sm:right-8 top-16 w-56 bg-ink-900 border border-wine-800 rounded-2xl shadow-2xl py-2 z-50">

                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition">
                                <svg class="w-4 h-4 shrink-0 text-gold-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profil
                            </a>

                            <a href="{{ route('books.myBooks') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition">
                                <svg class="w-4 h-4 shrink-0 text-gold-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Daftar Buku Saya
                            </a>

                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition">
                                <svg class="w-4 h-4 shrink-0 text-gold-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Wishlist
                            </a>

                            <hr class="border-wine-800/80 my-1.5">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-red-300 hover:text-red-100 hover:bg-red-900/40 transition">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </nav>

        <!-- Main Content: Detail Buku -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 pt-8 pb-16">

            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs text-stone-400 mb-8">
                <a href="{{ url('/') }}" class="hover:text-wine-700 transition">Beranda</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('books.index') }}" class="hover:text-wine-700 transition">Katalog</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-stone-600 font-semibold truncate max-w-[200px]">{{ $book->title }}</span>
            </nav>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-medium flex items-center gap-3" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                    <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">&times;</button>
                </div>
            @endif
            @if(session('info'))
                <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-5 py-3.5 rounded-2xl text-sm font-medium flex items-center gap-3" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('info') }}
                    <button @click="show = false" class="ml-auto text-blue-400 hover:text-blue-600">&times;</button>
                </div>
            @endif

            <!-- Book Detail Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

                <!-- Cover Buku (Kiri) -->
                <div class="lg:col-span-4">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-3xl border border-stone-200/80 shadow-lg overflow-hidden p-4">
                            <div class="w-full aspect-[3/4] bg-stone-100 rounded-2xl overflow-hidden border border-ink-900/10">
                                @if($book->cover_image && $book->cover_image !== 'default_cover.jpg')
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-stone-100 text-stone-300 p-4 text-center">
                                        <svg class="w-20 h-20 mb-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2zm-3 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-6 6h6v1H9v-1z"/>
                                        </svg>
                                        <span class="text-xs text-stone-400">Tidak ada sampul</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Wishlist Toggle -->
                        @auth
                        <div class="mt-4">
                            <form action="{{ route('wishlist.toggle', $book->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl border border-stone-200 bg-white hover:bg-wine-50 text-sm font-semibold text-stone-700 hover:text-wine-700 transition shadow-sm">
                                    <svg class="w-5 h-5 {{ $book->wishlistedBy->contains(Auth::id()) ? 'text-red-500 fill-red-500' : 'text-stone-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    {{ $book->wishlistedBy->contains(Auth::id()) ? 'Hapus dari Wishlist' : 'Tambahkan ke Wishlist' }}
                                </button>
                            </form>
                        </div>
                        @endauth
                    </div>
                </div>

                <!-- Info & Aksi Buku (Kanan) -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Kategori Badge -->
                    @if($book->categories->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach($book->categories as $cat)
                            <span class="inline-flex items-center gap-1.5 bg-wine-50 text-wine-800 text-xs font-semibold px-3 py-1.5 rounded-full border border-wine-200">
                                {{ $cat->name }}
                            </span>
                        @endforeach
                    </div>
                    @endif

                    <!-- Judul -->
                    <h1 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-ink-900 leading-tight">
                        {{ $book->title }}
                    </h1>

                    <!-- Penulis & Penerbit -->
                    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-stone-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Penulis: <strong class="text-stone-900">{{ $book->author }}</strong></span>
                        </div>
                        @if($book->publisher)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>Penerbit: <strong class="text-stone-900">{{ $book->publisher }}</strong></span>
                        </div>
                        @endif
                    </div>

                    <!-- Harga -->
                    <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Harga</span>
                                <div class="mt-1">
                                    @if($book->price == 0)
                                        <span class="text-2xl font-black text-emerald-600">GRATIS</span>
                                    @else
                                        <span class="text-2xl sm:text-3xl font-black text-wine-800" id="display-price">
                                            Rp {{ number_format($book->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($isPurchased)
                                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold px-4 py-2 rounded-full border border-emerald-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Sudah Dibeli
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Aksi: Beli / Baca -->
                    <div class="space-y-4">
                        @if($isPurchased)
                            <!-- Tombol Baca Buku -->
                            <a href="{{ route('books.read', $book->id) }}"
                               class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-wine-700 to-wine-800 hover:from-wine-800 hover:to-wine-900 text-white py-4 rounded-2xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Baca Buku Sekarang
                            </a>
                        @else
                            @auth
                                <!-- Input Voucher -->
                                <div class="bg-wine-50/80 border border-wine-200/60 rounded-2xl p-4" x-data="{ voucherCode: '', voucherApplied: false, discountedPrice: null, discountLabel: '', applyingVoucher: false }">
                                    <label class="text-xs font-bold text-stone-600 uppercase tracking-wider mb-2 block">Punya Kode Voucher?</label>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="voucherCode" placeholder="Masukkan kode voucher..."
                                               class="flex-1 bg-white border border-stone-200 rounded-xl px-4 py-2.5 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-gold-400/30 focus:border-gold-400 transition"
                                               id="voucher_code_input"
                                               :disabled="voucherApplied">
                                        <button @click="
                                            if (!voucherCode.trim()) return;
                                            applyingVoucher = true;
                                            fetch('{{ route('voucher.apply') }}', {
                                                method: 'POST',
                                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                                body: JSON.stringify({ code: voucherCode, book_id: {{ $book->id }}, price: {{ $book->price }} })
                                            })
                                            .then(r => r.json())
                                            .then(data => {
                                                applyingVoucher = false;
                                                if (data.success) {
                                                    voucherApplied = true;
                                                    discountedPrice = data.final_price;
                                                    discountLabel = data.discount_label || '';
                                                    document.getElementById('display-price').innerHTML = '<span class=\'line-through text-stone-400 text-lg mr-2\'>Rp {{ number_format($book->price, 0, ',', '.') }}</span> Rp ' + new Intl.NumberFormat('id-ID').format(data.final_price);
                                                } else {
                                                    alert(data.message || 'Voucher tidak valid.');
                                                }
                                            })
                                            .catch(() => { applyingVoucher = false; alert('Gagal memverifikasi voucher.'); });
                                        "
                                        class="px-5 py-2.5 bg-gold-500 hover:bg-gold-400 text-ink-900 font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-1.5"
                                        :disabled="voucherApplied || applyingVoucher"
                                        :class="voucherApplied ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'">
                                            <template x-if="applyingVoucher">
                                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                            </template>
                                            <span x-text="voucherApplied ? 'Terpakai' : 'Terapkan'"></span>
                                        </button>
                                    </div>
                                    <template x-if="voucherApplied">
                                        <p class="text-xs text-emerald-600 font-semibold mt-2">Voucher berhasil diterapkan! <span x-text="discountLabel"></span></p>
                                    </template>
                                </div>

                                <!-- Tombol Bayar -->
                                <button id="pay-button"
                                        data-book-id="{{ $book->id }}"
                                        data-csrf-token="{{ csrf_token() }}"
                                        class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-gold-500 to-gold-400 hover:from-gold-400 hover:to-gold-300 text-ink-900 py-4 rounded-2xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Beli & Bayar Sekarang
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                   class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-gold-500 to-gold-400 hover:from-gold-400 hover:to-gold-300 text-ink-900 py-4 rounded-2xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Login untuk Membeli Buku
                                </a>
                            @endauth
                        @endif
                    </div>

                    <!-- Deskripsi Buku -->
                    @if($book->description)
                    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-3">
                        <h3 class="font-serif text-lg font-bold text-ink-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-wine-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Deskripsi Buku
                        </h3>
                        <div class="text-sm text-stone-600 leading-relaxed prose prose-sm max-w-none">
                            {!! nl2br(e($book->description)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Info Tambahan -->
                    <div class="bg-wine-50/60 border border-wine-200/50 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center gap-2.5 mb-4">
                            <svg class="w-5 h-5 text-wine-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="font-serif text-lg font-bold text-ink-900">Informasi</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center gap-3 bg-white/80 rounded-xl px-4 py-3 border border-wine-100">
                                <svg class="w-4 h-4 text-gold-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="text-stone-600">Format: <strong class="text-stone-900">E-Book Digital</strong></span>
                            </div>
                            <div class="flex items-center gap-3 bg-white/80 rounded-xl px-4 py-3 border border-wine-100">
                                <svg class="w-4 h-4 text-gold-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-stone-600">Akses: <strong class="text-stone-900">Selamanya</strong></span>
                            </div>
                            <div class="flex items-center gap-3 bg-white/80 rounded-xl px-4 py-3 border border-wine-100">
                                <svg class="w-4 h-4 text-gold-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span class="text-stone-600">Baca di: <strong class="text-stone-900">Browser / HP</strong></span>
                            </div>
                            <div class="flex items-center gap-3 bg-white/80 rounded-xl px-4 py-3 border border-wine-100">
                                <svg class="w-4 h-4 text-gold-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span class="text-stone-600">Pembayaran: <strong class="text-stone-900">Aman via Midtrans</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    @php
                        $reviews = $book->reviews()->with('user')->latest()->get();
                    @endphp
                    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-5">
                        <h3 class="font-serif text-lg font-bold text-ink-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Ulasan Pembaca ({{ $reviews->count() }})
                        </h3>

                        <!-- Form Tulis Review (Hanya jika sudah login & sudah beli) -->
                        @auth
                            @if($isPurchased)
                            <form action="{{ route('reviews.store', $book->id) }}" method="POST" class="bg-stone-50 border border-stone-100 rounded-xl p-4 space-y-3">
                                @csrf
                                <label class="text-xs font-bold text-stone-600">Tulis ulasanmu:</label>
                                <textarea name="comment" rows="3" required placeholder="Bagikan pendapatmu tentang buku ini..."
                                          class="w-full bg-white border border-stone-200 rounded-xl px-4 py-3 text-xs text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-gold-400/30 focus:border-gold-400 transition resize-none"></textarea>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1" x-data="{ rating: 5 }">
                                        <input type="hidden" name="rating" :value="rating">
                                        <template x-for="star in 5" :key="star">
                                            <button type="button" @click="rating = star" class="focus:outline-none">
                                                <svg class="w-5 h-5 transition" :class="star <= rating ? 'text-gold-400' : 'text-stone-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                    <button type="submit" class="px-5 py-2 bg-wine-700 hover:bg-wine-800 text-white font-bold text-xs rounded-xl transition shadow-sm">Kirim Ulasan</button>
                                </div>
                            </form>
                            @endif
                        @endauth

                        <!-- Daftar Review -->
                        @if($reviews->count() > 0)
                            <div class="space-y-4">
                                @foreach($reviews as $review)
                                <div class="bg-stone-50 border border-stone-100 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 bg-wine-200 rounded-full flex items-center justify-center text-wine-800 font-bold text-xs">
                                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 2)) }}
                                            </div>
                                            <div>
                                                <span class="text-xs font-bold text-stone-900">{{ $review->user->name ?? 'Pengguna' }}</span>
                                                <span class="text-[10px] text-stone-400 ml-2">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @if(Auth::check() && (Auth::id() == $review->user_id || Auth::user()->email === 'rafibadillah8@gmail.com'))
                                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-stone-400 hover:text-red-500 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-0.5 mb-2">
                                        @for($i = 0; $i < ($review->rating ?? 5); $i++)
                                        <svg class="w-3.5 h-3.5 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <p class="text-xs text-stone-600 leading-relaxed">{{ $review->comment ?? $review->content }}</p>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-stone-400 italic">Belum ada ulasan untuk buku ini.</p>
                        @endif
                    </div>

                </div>
            </div>

        </main>
    </div>

    <!-- Footer Simple -->
    <footer class="border-t border-wine-900/20 bg-gradient-to-r from-wine-900 via-ink-900 to-wine-900 mt-20 py-8 text-xs text-wine-200">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2.5 font-bold text-wine-50 text-sm">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka" class="h-7 w-7 object-contain">
                <span>Ruang Pustaka</span>
            </div>
            <p class="text-wine-300/80">&copy; {{ date('Y') }} Ruang Pustaka. Semua hak cipta dilindungi.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        function bookSearch() {
            return {
                query: '',
                open: false,
                results: [],
                loading: false,
                async search() {
                    if (!this.query) { this.open = false; this.results = []; return; }
                    this.loading = true;
                    try {
                        const res = await fetch(`/books/search-suggestions?q=${encodeURIComponent(this.query)}`);
                        this.results = await res.json();
                    } catch (e) {
                        this.results = [];
                    } finally {
                        this.loading = false;
                        this.open = true;
                    }
                }
            }
        }
    </script>
    @auth
    @if(!$isPurchased &&$book->price > 0)
    <script src="{{ asset('js/book-payment.js') }}?v={{ filemtime(public_path('js/book-payment.js')) }}"></script>
    @endif
    @endauth
</body>
</html>