<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Pustaka - Perpustakaan & Arsip Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="md:hidden p-2 text-wine-200 hover:text-gold-400 hover:bg-wine-800/60 rounded-xl transition" title="Cari Buku">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold text-wine-100 hover:text-gold-400 px-3.5 py-2 transition">Log in</a>
                    <a href="{{ route('register') }}" class="text-xs sm:text-sm font-bold bg-gold-500 hover:bg-gold-400 text-ink-900 px-4 py-2 rounded-xl transition shadow-md hover:shadow-lg">Register</a>
                @endguest

                @auth
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

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 pt-8 pb-16 space-y-12">

            <!-- Banner Slider Otomatis -->
            <div class="relative overflow-hidden rounded-3xl shadow-xl shadow-wine-950/20 border border-wine-900/10 select-none"
                 x-data="{
                     activeSlide: 0,
                     totalSlides: {{ $activeVoucher ? 2 : 1 }},
                     autoplayInterval: null,
                     copiedVoucher: false,
                     touchStartX: 0,
                     touchEndX: 0,
                     copyVoucherCode(code) {
                         navigator.clipboard.writeText(code);
                         this.copiedVoucher = true;
                         setTimeout(() => { this.copiedVoucher = false; }, 2000);
                     },
                     init() {
                         this.startAutoplay();
                     },
                     startAutoplay() {
                         this.stopAutoplay();
                         this.autoplayInterval = setInterval(() => {
                             this.nextSlide();
                         }, 6000);
                     },
                     stopAutoplay() {
                         if (this.autoplayInterval) clearInterval(this.autoplayInterval);
                     },
                     nextSlide() {
                         this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                     },
                     prevSlide() {
                         this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
                     },
                     handleTouchStart(e) {
                         this.stopAutoplay();
                         this.touchStartX = e.changedTouches[0].screenX;
                     },
                     handleTouchEnd(e) {
                         this.touchEndX = e.changedTouches[0].screenX;
                         this.handleSwipe();
                         this.startAutoplay();
                     },
                     handleSwipe() {
                         if (this.touchEndX < this.touchStartX - 40) {
                             this.nextSlide();
                         }
                         if (this.touchEndX > this.touchStartX + 40) {
                             this.prevSlide();
                         }
                     }
                 }"
                 @mouseenter="stopAutoplay()"
                 @mouseleave="startAutoplay()"
                 @touchstart="handleTouchStart($event)"
                 @touchend="handleTouchEnd($event)">

                @if($activeVoucher)
                <button @click="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 z-40 bg-black/60 hover:bg-gold-500 hover:text-stone-900 text-gold-400 backdrop-blur-md p-3 rounded-full transition-all duration-300 flex items-center justify-center cursor-pointer shadow-xl border border-white/20 active:scale-95" aria-label="Sebelumnya">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 z-40 bg-black/60 hover:bg-gold-500 hover:text-stone-900 text-gold-400 backdrop-blur-md p-3 rounded-full transition-all duration-300 flex items-center justify-center cursor-pointer shadow-xl border border-white/20 active:scale-95" aria-label="Berikutnya">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                @endif

                <div class="relative w-full flex transition-transform duration-700 ease-out"
                     :style="`transform: translateX(-${activeSlide * 100}%)`">

                    <!-- SLIDE 1 -->
                    <div class="w-full shrink-0 bg-gradient-to-r from-wine-900 via-wine-800 to-ink-900 text-white p-7 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-8 relative">
                        <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -left-16 -top-16 w-72 h-72 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="relative z-10 max-w-xl text-center md:text-left space-y-4">
                            <span class="inline-flex items-center gap-1.5 bg-gold-500/20 text-gold-400 text-xs font-bold uppercase tracking-wider px-3.5 py-1.5 rounded-full border border-gold-500/30">
                                 Toko Buku Digital Original
                            </span>
                            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight leading-tight">
                                Belajar lebih mudah, membaca lebih praktis!
                            </h1>
                            <p class="text-wine-100/90 text-xs sm:text-sm leading-relaxed">
                                Temukan e-book pilihan untuk mendukung perkuliahan, pembelajaran, dan pengembangan pengetahuan. Baca langsung di <strong class="text-white font-semibold">Ruang Pustaka</strong> kapan saja dan di mana saja.
                            </p>

                            <div class="pt-4 flex flex-col sm:flex-row items-center gap-4">
                                <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="bg-gold-500 hover:bg-gold-400 text-ink-900 font-bold text-sm px-6 py-3 rounded-2xl transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    Jelajahi Koleksi
                                </a>
                                <div class="inline-flex items-center gap-2.5 bg-black/30 border border-white/10 px-4 py-2.5 rounded-2xl text-[10px] sm:text-xs text-wine-100 text-left backdrop-blur-md">
                                    <svg class="w-4 h-4 text-gold-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>E-book dibaca di platform ini (bukan PDF).</span>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 shrink-0 hidden md:flex items-center justify-center w-64 h-64">
                            <div class="relative w-40 h-56 transform transition hover:scale-105 duration-500 hover:rotate-2 group">
                                <div class="absolute inset-0 bg-stone-200 rounded-r-2xl rounded-l-md border border-stone-300 shadow-2xl transform rotate-6 translate-x-4 translate-y-4 flex flex-col p-4 opacity-50"></div>
                                <div class="absolute inset-0 bg-wine-200 rounded-r-2xl rounded-l-md border border-wine-300 shadow-2xl transform rotate-3 translate-x-2 translate-y-2 flex flex-col p-4 opacity-75"></div>
                                <div class="absolute inset-0 bg-white rounded-r-2xl rounded-l-md border border-stone-200 shadow-2xl flex flex-col items-center justify-center p-4 z-10 relative overflow-hidden group-hover:-translate-y-2 transition duration-500">
                                    <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-stone-300 to-stone-100 border-r border-stone-200"></div>
                                    <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka" class="w-16 h-16 object-contain mb-3 drop-shadow-md">
                                    <span class="block text-center text-sm font-extrabold tracking-wide text-ink-900 font-serif">Ruang<br>Pustaka</span>
                                    <div class="mt-4 border-t-2 border-gold-400 w-8"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($activeVoucher)
                    @php
                        $discountLabel = $activeVoucher->type === 'percentage'
                            ? number_format($activeVoucher->value, 0) . '%'
                            : 'Rp ' . number_format($activeVoucher->value, 0, ',', '.');
                    @endphp

                    <!-- SLIDE 2: Voucher -->
                    <div class="w-full shrink-0 bg-gradient-to-br from-ink-900 via-wine-900 to-wine-800 p-7 sm:p-10 text-white relative">
                        <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-gold-500/10 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                            <div class="lg:col-span-7 space-y-4">
                                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/20 text-xs font-semibold tracking-wide text-wine-100">
                                    <span class="flex h-2 w-2 rounded-full bg-gold-500 animate-pulse"></span>
                                    <span>Promo Spesial E-Book Bulan Ini</span>
                                </div>

                                <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight leading-tight">
                                    Belajar Lebih Praktis, Hemat dengan Voucher E-Book!
                                </h1>

                                <p class="text-wine-100/90 text-xs sm:text-sm leading-relaxed max-w-xl">
                                    Temukan ribuan koleksi e-book pilihan untuk mendukung pembelajaran dan kuliahmu. Gunakan kode voucher di bawah ini saat pembelian untuk klaim diskonnya!
                                </p>

                                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 max-w-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2.5 bg-gold-500/20 text-gold-400 rounded-xl border border-gold-500/30 shrink-0">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-wine-200 tracking-wider">Voucher Diskon {{ $discountLabel }} E-Book</span>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <code class="font-mono text-base font-extrabold text-gold-400 tracking-wider bg-black/25 px-2.5 py-0.5 rounded-lg border border-gold-400/30">
                                                    {{ $activeVoucher->code }}
                                                </code>
                                                <button @click="copyVoucherCode(@js($activeVoucher->code))" class="px-2.5 py-1 bg-gold-500 hover:bg-gold-400 text-ink-900 font-bold text-[11px] rounded-lg transition flex items-center gap-1 shadow-sm cursor-pointer">
                                                    <span x-text="copiedVoucher ? 'Tersalin!' : 'Salin'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-5 flex justify-center lg:justify-end">
                                <div class="w-full max-w-xs bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-6 flex flex-col items-center text-center shadow-2xl relative overflow-hidden group hover:scale-105 transition-all duration-300">
                                    <div class="absolute -top-1 -right-1 bg-gold-500 text-stone-900 text-[10px] font-black uppercase px-4 py-1.5 rounded-bl-2xl shadow-md tracking-wider">
                                        DISKON {{ $discountLabel }}
                                    </div>
                                    <div class="w-24 h-24 rounded-2xl bg-white flex items-center justify-center shadow-lg p-2 mb-4 transform transition hover:scale-105">
                                        <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka" class="w-full h-full object-contain">
                                    </div>
                                    <h3 class="font-serif text-lg font-bold text-white">Ruang Pustaka</h3>
                                    <p class="text-[11px] font-semibold text-wine-200 uppercase tracking-widest mt-0.5">Voucher E-Book Digital</p>
                                    <div class="mt-5 pt-4 border-t border-white/15 w-full flex items-center justify-between text-xs">
                                        <span class="text-wine-200">Masa Berlaku:</span>
                                        <span class="font-bold text-gold-400">
                                            {{ $activeVoucher->expires_at ? $activeVoucher->expires_at->translatedFormat('d M Y') : 'Tanpa Batas Waktu' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                <div class="absolute bottom-4 left-0 right-0 z-20 flex justify-center items-center space-x-2">
                    <button @click="activeSlide = 0; stopAutoplay(); startAutoplay();" 
                            :class="activeSlide === 0 ? 'bg-gold-400 w-7' : 'bg-white/40 w-2.5 hover:bg-white/70'" 
                            class="h-2.5 rounded-full transition-all duration-300 cursor-pointer shadow-sm"
                            aria-label="Slide 1">
                    </button>
                    @if($activeVoucher)
                    <button @click="activeSlide = 1; stopAutoplay(); startAutoplay();" 
                            :class="activeSlide === 1 ? 'bg-gold-400 w-7' : 'bg-white/40 w-2.5 hover:bg-white/70'" 
                            class="h-2.5 rounded-full transition-all duration-300 cursor-pointer shadow-sm"
                            aria-label="Slide 2">
                    </button>
                    @endif
                </div>          
            </div>

            <!-- ==================== KATEGORI / FILTER GENRE ==================== -->
            @if(count($categories ?? []) > 0)
            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-wine-700 rounded-full"></span>
                    <h2 class="text-xs font-bold text-stone-500 uppercase tracking-widest">Jelajahi Kategori Cepat</h2>
                </div>
                <div class="flex gap-2.5 overflow-x-auto no-scrollbar pb-1">
                    @foreach($categories as $category)
                        @if(count(data_get($category, 'books', [])) > 0)
                        <a href="#kategori-{{ data_get($category, 'id') }}"
                           class="shrink-0 px-4 py-2 rounded-2xl text-xs font-semibold bg-white border border-stone-200/80 text-stone-700 hover:border-wine-700 hover:text-wine-800 hover:bg-wine-50/50 transition shadow-xs flex items-center gap-1.5">
                            <span>{{ data_get($category, 'name', 'Kategori') }}</span>
                        </a>
                        @endif
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Helper Blade Template Component/Snippet for Book Card to avoid repetition -->
            @php
                $renderBookCard = function($book) {
                    $targetUrl = Auth::check() ? route('books.show', data_get($book, 'id', 1)) : route('login');
                    return '<div class="w-[180px] sm:w-[200px] shrink-0 bg-wine-100/90 border border-wine-300/80 rounded-3xl p-3.5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between group/card">
                        <div>
                            <a href="'.$targetUrl.'" class="block w-full aspect-[3/4] bg-stone-100 rounded-2xl overflow-hidden mb-3.5 relative border border-ink-900/10 shadow-xs">
                                '.(data_get($book, 'cover_image') 
                                    ? '<img src="'.asset('storage/' . data_get($book, 'cover_image')).'" alt="'.data_get($book, 'title').'" class="w-full h-full object-cover group-hover/card:scale-105 transition duration-500">' 
                                    : '<div class="w-full h-full flex flex-col items-center justify-center bg-stone-100 text-stone-300 p-2 text-center">
                                        <svg class="w-12 h-12 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2zm-3 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-6 6h6v1H9v-1z"/></svg>
                                        <span class="text-[10px] text-stone-400">Tidak ada sampul</span>
                                       </div>').'
                            </a>
                            <a href="'.$targetUrl.'" class="font-bold text-xs sm:text-sm text-stone-900 line-clamp-1 block hover:text-wine-700 transition" title="'.data_get($book, 'title').'">
                                '.data_get($book, 'title', 'Judul Buku').'
                            </a>
                            <p class="text-[11px] text-stone-500 flex items-center gap-1 mt-1 truncate">
                                <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="truncate">'.data_get($book, 'author', 'Penulis Tidak Diketahui').'</span>
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-wine-200/60 flex items-center justify-between">
                            <span class="font-extrabold text-xs sm:text-sm text-stone-900">
                                Rp '.number_format(data_get($book, 'price', 0), 0, ',', '.').'
                            </span>
                            <a href="'.$targetUrl.'" class="p-2 bg-wine-700 hover:bg-wine-800 border border-wine-700 rounded-xl text-white transition shadow-md hover:shadow-lg flex items-center justify-center" title="Detail / Beli Buku">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
                            </a>
                        </div>
                    </div>';
                };
            @endphp

            <!-- ==================== 1. BUKU POPULER ==================== -->
            <section class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2 h-6 bg-wine-700 rounded-full"></div>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold text-ink-900">Buku Populer</h2>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            @if(strtolower(Auth::user()->role ?? '') === 'admin' || (isset(Auth::user()->is_admin) && Auth::user()->is_admin))
                                <a href="{{ route('books.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Buku
                                </a>
                            @endif
                        @endauth

                        <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="text-xs sm:text-sm font-semibold text-wine-700 hover:text-wine-900 hover:underline flex items-center gap-1 transition">
                            Lihat Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <div class="relative group/slider">
                    @if(count($popularBooks ?? []) > 4)
                        <button onclick="scrollSlider('slider-popular', 'left')" class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button onclick="scrollSlider('slider-popular', 'right')" class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif

                    <div id="slider-popular" class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
                        @forelse($popularBooks ?? [] as $book)
                            {!! $renderBookCard($book) !!}
                        @empty
                            <div class="w-full py-12 text-center bg-white rounded-3xl border border-dashed border-stone-300 text-stone-400 text-xs">
                                Belum ada buku populer saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- ==================== REKOMENDASI UNTUKMU ==================== -->
            @if(count($recommendedBooks ?? []) > 0)
            <section class="space-y-4 bg-wine-50/80 border border-wine-200/60 rounded-3xl p-6 sm:p-8 shadow-sm">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-6 bg-gold-500 rounded-full"></div>
                        <div>
                            <h2 class="font-serif text-xl sm:text-2xl font-bold text-ink-900">Rekomendasi Untukmu</h2>
                            <p class="text-xs text-stone-500">Dipilih berdasarkan minat pembaca lain</p>
                        </div>
                    </div>
                </div>

                <div class="relative group/slider">
                    @if(count($recommendedBooks) > 4)
                        <button onclick="scrollSlider('slider-recommended', 'left')" class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button onclick="scrollSlider('slider-recommended', 'right')" class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif

                    <div id="slider-recommended" class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
                        @foreach($recommendedBooks as $book)
                            {!! $renderBookCard($book) !!}
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            <!-- ==================== 2. BUKU BARU (< 24 JAM) ==================== -->
            @if(count($newBooks ?? []) > 0)
            <section class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2 h-6 bg-emerald-500 rounded-full"></div>
                        <h2 class="font-serif text-xl sm:text-2xl font-bold text-ink-900">Buku Baru</h2>
                        <span class="bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-extrabold px-3 py-0.5 rounded-full">&lt; 24 Jam</span>
                    </div>
                    <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="text-xs sm:text-sm font-semibold text-wine-700 hover:text-wine-900 hover:underline flex items-center gap-1 transition">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="relative group/slider">
                    @if(count($newBooks) > 4)
                        <button onclick="scrollSlider('slider-new', 'left')" class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button onclick="scrollSlider('slider-new', 'right')" class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif

                    <div id="slider-new" class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
                        @foreach($newBooks as $book)
                            {!! $renderBookCard($book) !!}
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            <!-- ==================== 3. KATEGORI BUKU ==================== -->
            @foreach($categories ?? [] as $category)
                @if(count(data_get($category, 'books', [])) > 0)
                <section id="kategori-{{ data_get($category, 'id') }}" class="space-y-4 scroll-mt-28">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2.5">
                            <div class="w-2 h-6 bg-wine-700 rounded-full"></div>
                            <h2 class="font-serif text-xl sm:text-2xl font-bold text-ink-900">{{ data_get($category, 'name', 'Kategori') }}</h2>
                        </div>
                        <a href="{{ Auth::check() ? route('books.index') : route('login') }}" class="text-xs sm:text-sm font-semibold text-wine-700 hover:text-wine-900 hover:underline flex items-center gap-1 transition">
                            Lihat Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="relative group/slider">
                        @if(count(data_get($category, 'books', [])) > 4)
                            <button onclick="scrollSlider('slider-cat-{{ data_get($category, 'id') }}', 'left')" class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button onclick="scrollSlider('slider-cat-{{ data_get($category, 'id') }}', 'right')" class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        @endif

                        <div id="slider-cat-{{ data_get($category, 'id') }}" class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
                            @foreach(data_get($category, 'books', []) as $book)
                                {!! $renderBookCard($book) !!}
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif
            @endforeach

            <!-- ==================== TESTIMONI / TRUST SECTION ==================== -->
            <section class="mt-16 bg-white border border-stone-200/60 rounded-3xl p-8 sm:p-12 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-wine-50 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-wine-50 text-wine-800 px-3 py-1.5 rounded-full text-xs font-bold mb-4 border border-wine-100">
                            <span class="w-2 h-2 rounded-full bg-wine-600"></span>
                            Dipercaya oleh pembaca se-Nusantara
                        </div>
                        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-ink-900 leading-tight mb-4">
                            Platform E-Book Favorit untuk Menunjang Produktivitasmu
                        </h2>
                        <p class="text-sm text-stone-500 leading-relaxed mb-8">
                            Ruang Pustaka memberikan kemudahan akses buku digital original kapan saja dan di mana saja.
                        </p>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <span class="block text-3xl font-black text-wine-800 font-serif">500+</span>
                                <span class="text-xs font-semibold text-stone-500 uppercase tracking-widest mt-1 block">Koleksi E-Book</span>
                            </div>
                            <div>
                                <span class="block text-3xl font-black text-gold-500 font-serif">10k+</span>
                                <span class="text-xs font-semibold text-stone-500 uppercase tracking-widest mt-1 block">Pengguna Aktif</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4 relative">
                        <div class="bg-stone-50 p-5 rounded-2xl border border-stone-200 shadow-sm relative z-10 transform md:-translate-x-8 hover:-translate-y-1 transition duration-300">
                            <div class="flex items-center gap-1 mb-3">
                                @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <p class="text-xs sm:text-sm text-stone-700 italic mb-4">"Sangat membantu untuk cari referensi tugas kuliah. Aksesnya cepat dan harganya terjangkau pakai voucher!"</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-wine-200 rounded-full flex items-center justify-center text-wine-800 font-bold text-xs">AD</div>
                                <div>
                                    <span class="block text-xs font-bold text-ink-900">Andi</span>
                                    <span class="block text-[10px] text-stone-500">Mahasiswa</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-wine-900 text-white p-5 rounded-2xl shadow-xl relative z-20 transform md:translate-x-4 hover:-translate-y-1 transition duration-300">
                            <div class="flex items-center gap-1 mb-3">
                                @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <p class="text-xs sm:text-sm text-wine-50 italic mb-4">"Aplikasinya keren, bacanya nyaman banget di mata. Lebih praktis daripada bawa buku fisik tebal-tebal."</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gold-400 rounded-full flex items-center justify-center text-ink-900 font-bold text-xs">SR</div>
                                <div>
                                    <span class="block text-xs font-bold text-white">Siti Rahma</span>
                                    <span class="block text-[10px] text-wine-300">Penggemar Fiksi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

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

    <!-- Script Slider Horisontal & Salin Promo -->
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

        function scrollSlider(elementId, direction) {
            const container = document.getElementById(elementId);
            if (!container) return;
            const scrollAmount = 340;
            container.scrollBy({
                left: direction === 'left' ? -scrollAmount : scrollAmount,
                behavior: 'smooth'
            });
        }
    </script>
</body>
</html>