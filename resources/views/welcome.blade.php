<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Pustaka - Perpustakaan & Arsip Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important
        }

        .font-serif {
            font-family: 'Fraunces', Georgia, serif
        }

        .font-sans,
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif
        }

        body {
            background-image: radial-gradient(900px 500px at 100% 0, rgba(184, 134, 59, .10), transparent 60%), radial-gradient(700px 500px at 0 40%, rgba(111, 127, 94, .08), transparent 60%)
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible {
            outline: 2px solid #C79A4B;
            outline-offset: 3px
        }

        #rp-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 100%;
            transform: scaleX(0);
            transform-origin: left;
            background: linear-gradient(90deg, #B8863B, #E3BE7C);
            z-index: 60
        }

        .rp-nav {
            backdrop-filter: blur(14px);
            transition: padding .25s, box-shadow .25s
        }

        .rp-nav.is-scrolled {
            padding-top: .5rem;
            padding-bottom: .5rem;
            box-shadow: 0 10px 30px -12px rgba(0, 0, 0, .45)
        }

        .rp-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 5;
            background-image: radial-gradient(rgba(255, 255, 255, .07) 1px, transparent 1px);
            background-size: 3px 3px;
            opacity: .55
        }

        .rp-rise>* {
            animation: rpIn .7s cubic-bezier(.2, .7, .2, 1) both
        }

        .rp-rise>:nth-child(2) {
            animation-delay: .1s
        }

        .rp-rise>:nth-child(3) {
            animation-delay: .2s
        }

        .rp-rise>:nth-child(4) {
            animation-delay: .3s
        }

        @keyframes rpIn {
            from {
                opacity: 0;
                transform: translateY(14px)
            }
        }

        .rp-book {
            transform: perspective(900px) rotateX(var(--rx, 4deg)) rotateY(var(--ry, -12deg));
            transform-style: preserve-3d;
            transition: transform .25s ease-out
        }

        .rp-card {
            transition: transform .3s cubic-bezier(.2, .7, .2, 1), box-shadow .3s, border-color .3s
        }

        .rp-card:hover {
            transform: translateY(-4px);
            border-color: #C79A4B;
            box-shadow: 0 22px 34px -22px rgba(46, 29, 25, .55)
        }

        .rp-cover {
            transform-origin: left center;
            transition: transform .35s cubic-bezier(.2, .7, .2, 1), box-shadow .35s;
            box-shadow: 0 10px 18px -12px rgba(40, 20, 10, .6)
        }

        .rp-cover::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 9px;
            z-index: 2;
            pointer-events: none;
            background: linear-gradient(90deg, rgba(0, 0, 0, .28), rgba(255, 255, 255, .14))
        }

        .rp-card:hover .rp-cover {
            transform: perspective(700px) rotateY(-12deg);
            box-shadow: 12px 18px 24px -14px rgba(40, 20, 10, .55)
        }

        #rp-top {
            position: fixed;
            right: 20px;
            bottom: calc(20px + env(safe-area-inset-bottom, 0px));
            z-index: 50;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #2A1A17;
            color: #E3BE7C;
            border: 1px solid rgba(227, 190, 124, .4);
            display: grid;
            place-items: center;
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
            transition: .3s;
            cursor: pointer
        }

        #rp-top.on {
            opacity: 1;
            transform: none;
            pointer-events: auto
        }

        .rp-menu-panel {
            visibility: hidden
        }

        .rp-kbd {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            line-height: 18px;
            padding: 0 6px;
            border: 1px solid #d6d3d1;
            border-radius: 6px;
            color: #78716c;
            pointer-events: none;
            background: #fafaf9
        }

        .rp-nav {
            top: 12px !important;
            margin: 12px auto 0;
            width: min(1200px, calc(100% - 24px));
            border-radius: 999px;
            border: 1px solid rgba(227, 190, 124, .2) !important
        }

        .rp-hero {
            border-radius: 2rem !important
        }

        .rp-hero::before {
            content: "";
            position: absolute;
            right: -130px;
            top: -130px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            border: 1px solid rgba(227, 190, 124, .2);
            box-shadow: 0 0 0 60px rgba(227, 190, 124, .04), 0 0 0 120px rgba(227, 190, 124, .025);
            pointer-events: none;
            z-index: 1
        }

        .rp-hero .bg-gold-500 {
            border-radius: 999px !important
        }

        .rp-hero [class*="bg-black/30"] {
            border-radius: 999px !important
        }

        .rp-ticket {
            border-radius: 22px !important
        }

        .rp-ticket::before,
        .rp-ticket::after {
            content: "";
            position: absolute;
            top: 62%;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #2d1d19
        }

        .rp-ticket::before {
            left: -11px
        }

        .rp-ticket::after {
            right: -11px
        }

        .rp-ticket .border-t {
            border-top-style: dashed !important
        }

        .rp-feats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px
        }

        .rp-feat {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            background: #FFFCF5;
            border: 1px solid #E4D9C5;
            border-radius: 22px;
            padding: 18px 20px;
            transition: transform .3s, border-color .3s, box-shadow .3s
        }

        .rp-feat:hover {
            transform: translateY(-3px);
            border-color: #C79A4B;
            box-shadow: 0 18px 30px -22px rgba(46, 29, 25, .5)
        }

        .rp-feat>span {
            flex: none;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #F0E2C4;
            display: grid;
            place-items: center;
            font-size: 20px
        }

        .rp-feat b {
            display: block;
            font-family: 'Fraunces', Georgia, serif;
            font-size: 17px;
            line-height: 1.25;
            color: #2B1D18
        }

        .rp-feat p {
            font-size: 13px;
            color: #75655B;
            margin-top: 4px
        }

        .rp-chip {
            transition: transform .2s, background .2s, color .2s, border-color .2s
        }

        .rp-chip:hover {
            background: #2A1A17;
            color: #F3EADB;
            border-color: #2A1A17;
            transform: translateY(-2px)
        }

        .rp-bar {
            width: 26px;
            height: 4px;
            border-radius: 99px;
            background: linear-gradient(90deg, #B8863B, #E3BE7C)
        }

        .rp-bar-g {
            background: linear-gradient(90deg, #6F7F5E, #A9B88F)
        }

        .rp-all {
            padding: 7px 16px;
            border: 1px solid #E4D9C5;
            border-radius: 999px;
            background: #FFFCF5;
            transition: .2s
        }

        .rp-all:hover {
            border-color: #B8863B;
            background: #F0E2C4;
            text-decoration: none !important
        }

        .rp-price {
            background: #F0E2C4;
            border-radius: 999px;
            padding: 4px 12px
        }

        .rp-card .rp-buy {
            border-radius: 999px !important;
            transition: transform .2s, background .2s
        }

        .rp-card:hover .rp-buy {
            transform: scale(1.08)
        }

        .rp-rec {
            background: linear-gradient(135deg, rgba(111, 127, 94, .14), rgba(255, 252, 245, .9));
            border: 1px solid #E4D9C5;
            border-radius: 2rem
        }

        .rp-trust {
            border-radius: 2rem !important;
            border-color: #E4D9C5 !important
        }

        .rp-foot {
            border-radius: 2.5rem 2.5rem 0 0
        }

        @media(max-width:768px) {
            .rp-feats {
                grid-template-columns: 1fr
            }

            .rp-nav {
                border-radius: 28px
            }
        }

        @media(prefers-reduced-motion:reduce) {

            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important
            }
        }
    </style>
</head>

<body
    class="bg-parchment text-ink-900 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-gold-500 selection:text-ink-900">

    <div class="flex-1">
        <div id="rp-progress" aria-hidden="true"></div>
        <button id="rp-top" aria-label="Kembali ke atas" onclick="window.scrollTo({top:0,behavior:'smooth'})"><svg
                class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
            </svg></button>
        <!-- Navbar Dynamic (Tamu vs Auth) -->
        <nav
            class="rp-nav border-b border-wine-900/20 bg-gradient-to-r from-wine-900 via-ink-900 to-wine-900 backdrop-blur-md px-4 sm:px-8 py-3.5 flex justify-between items-center sticky top-0 z-50 transition-all shadow-md">
            <a href="{{ url('/') }}"
                class="flex items-center gap-2.5 text-lg sm:text-xl font-bold text-gold-400 hover:text-gold-300 transition">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka"
                    class="h-10 w-10 object-contain drop-shadow">
                <span class="font-serif tracking-tight">Ruang Pustaka</span>
            </a>

            <!-- Search Bar dengan Autocomplete (desktop) -->
            <div class="hidden md:block flex-1 max-w-lg mx-6" x-data="bookSearch()">
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="open = query.length > 0"
                        @click.outside="open = false" placeholder="Cari judul, penulis, atau kategori..."
                        class="w-full bg-white/95 border border-wine-800/20 focus:bg-white focus:border-gold-400 rounded-full pl-10 pr-10 py-2.5 text-sm text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-gold-400/30 transition shadow-inner">
                    <div x-cloak x-show="open" x-transition
                        class="absolute mt-2 w-full bg-white rounded-2xl shadow-2xl border border-stone-200 overflow-hidden z-50">
                        <template x-if="loading">
                            <p class="px-4 py-3 text-xs text-stone-400">Mencari...</p>
                        </template>
                        <template x-if="!loading && results.length === 0">
                            <p class="px-4 py-3 text-xs text-stone-400">Tidak ada hasil untuk "<span x-text="query"
                                    class="font-semibold text-stone-600"></span>"</p>
                        </template>
                        <template x-for="r in results" :key="r.id">
                            <a :href="r.url"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-wine-50/60 transition-colors border-b border-stone-100 last:border-none">
                                <img :src="r.cover" :alt="r.title"
                                    class="h-11 w-8 rounded-lg object-cover shrink-0 bg-stone-100 border border-stone-200 shadow-xs">
                                <span class="text-xs min-w-0">
                                    <span class="block font-bold text-stone-900 truncate" x-text="r.title"></span>
                                    <span class="block text-[11px] text-stone-500 truncate mt-0.5"
                                        x-text="r.author"></span>
                                </span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ Auth::check() ? route('books.index') : route('login') }}"
                    class="md:hidden p-2 text-wine-200 hover:text-gold-400 hover:bg-wine-800/60 rounded-xl transition"
                    title="Cari Buku">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </a>

                @guest
                    <a href="{{ route('login') }}"
                        class="text-xs sm:text-sm font-semibold text-wine-100 hover:text-gold-400 px-3.5 py-2 transition">Log
                        in</a>
                    <a href="{{ route('register') }}"
                        class="text-xs sm:text-sm font-bold bg-gold-500 hover:bg-gold-400 text-ink-900 px-4 py-2 rounded-xl transition shadow-md hover:shadow-lg">Register</a>
                @endguest

                @auth
                    <div class="relative flex items-center pl-2 border-l border-wine-800/50" x-data="profileMenu()"
                        @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu"
                            aria-label="Menu akun"
                            class="flex items-center gap-2 text-xs sm:text-sm font-medium text-wine-100 hover:text-gold-400 px-3 py-2 rounded-xl hover:bg-wine-800/60 transition-colors">
                            <span class="hidden sm:inline">Halo, <b
                                    class="text-gold-400">{{ Auth::user()->name }}</b></span>
                            <svg data-menu-chev class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div data-menu-panel role="menu" aria-label="Menu akun"
                            class="rp-menu-panel absolute right-0 top-full mt-4 w-56 bg-ink-900 border border-wine-800 rounded-2xl shadow-2xl py-2 z-50">

                            <a data-menu-item role="menuitem" href="{{ route('profile.edit') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition-colors">
                                <svg class="w-4 h-4 shrink-0 text-gold-400/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil
                            </a>

                            <a data-menu-item role="menuitem" href="{{ route('books.myBooks') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition-colors">
                                <svg class="w-4 h-4 shrink-0 text-gold-400/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Daftar Buku Saya
                            </a>

                            <a data-menu-item role="menuitem" href="{{ route('wishlist.index') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-xs font-medium text-wine-100 hover:text-gold-400 hover:bg-wine-800/60 transition-colors">
                                <svg class="w-4 h-4 shrink-0 text-gold-400/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                Wishlist
                            </a>

                            <hr class="border-wine-800/80 my-1.5">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button data-menu-item role="menuitem" type="submit"
                                    class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-xs font-semibold text-red-300 hover:text-red-100 hover:bg-red-900/40 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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
            <div class="relative overflow-hidden rp-hero rounded-3xl shadow-xl shadow-wine-950/20 border border-wine-900/10 select-none"
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
                 }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()" @touchstart="handleTouchStart($event)"
                @touchend="handleTouchEnd($event)">

                @if($activeVoucher)
                    <button @click="prevSlide()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 z-40 bg-black/60 hover:bg-gold-500 hover:text-stone-900 text-gold-400 backdrop-blur-md p-3 rounded-full transition-all duration-300 flex items-center justify-center cursor-pointer shadow-xl border border-white/20 active:scale-95"
                        aria-label="Sebelumnya">
                        <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="nextSlide()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 z-40 bg-black/60 hover:bg-gold-500 hover:text-stone-900 text-gold-400 backdrop-blur-md p-3 rounded-full transition-all duration-300 flex items-center justify-center cursor-pointer shadow-xl border border-white/20 active:scale-95"
                        aria-label="Berikutnya">
                        <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif

                <div class="relative w-full flex transition-transform duration-700 ease-out"
                    :style="`transform: translateX(-${activeSlide * 100}%)`">

                    <!-- SLIDE 1 -->
                    <div
                        class="w-full shrink-0 bg-gradient-to-r from-wine-900 via-wine-800 to-ink-900 text-white p-7 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-8 relative">
                        <div
                            class="absolute -right-16 -bottom-16 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute -left-16 -top-16 w-72 h-72 bg-gold-500/10 rounded-full blur-3xl pointer-events-none">
                        </div>

                        <div class="rp-rise relative z-10 max-w-xl text-center md:text-left space-y-4">
                            <span
                                class="inline-flex items-center gap-1.5 bg-gold-500/15 text-gold-400 text-xs font-semibold px-3.5 py-1.5 rounded-full border border-gold-500/30">
                                Toko Buku Digital Original
                            </span>
                            <h1
                                class="font-serif text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight leading-[1.05] text-balance">
                                Belajar lebih mudah, membaca lebih praktis!
                            </h1>
                            <p class="text-wine-100/90 text-xs sm:text-sm leading-relaxed">
                                Temukan e-book pilihan untuk mendukung perkuliahan, pembelajaran, dan pengembangan
                                pengetahuan. Baca langsung di <strong class="text-white font-semibold">Ruang
                                    Pustaka</strong> kapan saja dan di mana saja.
                            </p>

                            <div class="pt-4 flex flex-col sm:flex-row items-center gap-4">
                                <a href="{{ Auth::check() ? route('books.index') : route('login') }}"
                                    class="bg-gold-500 hover:bg-gold-400 text-ink-900 font-bold text-sm px-6 py-3 rounded-2xl transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                    Jelajahi Koleksi
                                </a>
                                <div
                                    class="inline-flex items-center gap-2.5 bg-black/30 border border-white/10 px-4 py-2.5 rounded-2xl text-[10px] sm:text-xs text-wine-100 text-left backdrop-blur-md">
                                    <svg class="w-4 h-4 text-gold-400 shrink-0" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>E-book dibaca di platform ini (bukan PDF).</span>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 shrink-0 hidden md:flex items-center justify-center w-64 h-64">
                            <div id="rp-book" class="rp-book relative w-40 h-56 group">
                                <div
                                    class="absolute inset-0 bg-stone-200 rounded-r-2xl rounded-l-md border border-stone-300 shadow-2xl transform rotate-6 translate-x-4 translate-y-4 flex flex-col p-4 opacity-50">
                                </div>
                                <div
                                    class="absolute inset-0 bg-wine-200 rounded-r-2xl rounded-l-md border border-wine-300 shadow-2xl transform rotate-3 translate-x-2 translate-y-2 flex flex-col p-4 opacity-75">
                                </div>
                                <div
                                    class="absolute inset-0 bg-white rounded-r-2xl rounded-l-md border border-stone-200 shadow-2xl flex flex-col items-center justify-center p-4 z-10 relative overflow-hidden group-hover:-translate-y-2 transition duration-500">
                                    <div
                                        class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-stone-300 to-stone-100 border-r border-stone-200">
                                    </div>
                                    <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka"
                                        class="w-16 h-16 object-contain mb-3 drop-shadow-md">
                                    <span
                                        class="block text-center text-sm font-extrabold tracking-wide text-ink-900 font-serif">Ruang<br>Pustaka</span>
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
                        <div
                            class="w-full shrink-0 bg-gradient-to-br from-ink-900 via-wine-900 to-wine-800 p-7 sm:p-10 text-white relative">
                            <div
                                class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none">
                            </div>
                            <div
                                class="absolute -bottom-12 -left-12 w-64 h-64 bg-gold-500/10 rounded-full blur-2xl pointer-events-none">
                            </div>

                            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                                <div class="lg:col-span-7 space-y-4">
                                    <div
                                        class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/20 text-xs font-semibold tracking-wide text-wine-100">
                                        <span class="flex h-2 w-2 rounded-full bg-gold-500 animate-pulse"></span>
                                        <span>Promo Spesial E-Book Bulan Ini</span>
                                    </div>

                                    <h1
                                        class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight leading-tight">
                                        Belajar Lebih Praktis, Hemat dengan Voucher E-Book!
                                    </h1>

                                    <p class="text-wine-100/90 text-xs sm:text-sm leading-relaxed max-w-xl">
                                        Temukan ribuan koleksi e-book pilihan untuk mendukung pembelajaran dan kuliahmu.
                                        Gunakan kode voucher di bawah ini saat pembelian untuk klaim diskonnya!
                                    </p>

                                    <div
                                        class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 max-w-lg">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="p-2.5 bg-gold-500/20 text-gold-400 rounded-xl border border-gold-500/30 shrink-0">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <span
                                                    class="text-[10px] uppercase font-bold text-wine-200 tracking-wider">Voucher
                                                    Diskon {{ $discountLabel }} E-Book</span>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <code
                                                        class="font-mono text-base font-extrabold text-gold-400 tracking-wider bg-black/25 px-2.5 py-0.5 rounded-lg border border-gold-400/30">
                                                        {{ $activeVoucher->code }}
                                                    </code>
                                                    <button @click="copyVoucherCode(@js($activeVoucher->code))"
                                                        class="px-2.5 py-1 bg-gold-500 hover:bg-gold-400 text-ink-900 font-bold text-[11px] rounded-lg transition flex items-center gap-1 shadow-sm cursor-pointer">
                                                        <span x-text="copiedVoucher ? 'Tersalin!' : 'Salin'"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                                    <div
                                        class="rp-ticket w-full max-w-xs bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-6 flex flex-col items-center text-center shadow-2xl relative overflow-hidden group hover:scale-105 transition-all duration-300">
                                        <div
                                            class="absolute -top-1 -right-1 bg-gold-500 text-stone-900 text-[10px] font-black uppercase px-4 py-1.5 rounded-bl-2xl shadow-md tracking-wider">
                                            DISKON {{ $discountLabel }}
                                        </div>
                                        <div
                                            class="w-24 h-24 rounded-2xl bg-white flex items-center justify-center shadow-lg p-2 mb-4 transform transition hover:scale-105">
                                            <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka"
                                                class="w-full h-full object-contain">
                                        </div>
                                        <h3 class="font-serif text-lg font-bold text-white">Ruang Pustaka</h3>
                                        <p class="text-[11px] font-semibold text-wine-200 uppercase tracking-widest mt-0.5">
                                            Voucher E-Book Digital</p>
                                        <div
                                            class="mt-5 pt-4 border-t border-white/15 w-full flex items-center justify-between text-xs">
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

            <!-- ==================== KEUNGGULAN ==================== -->
            <section class="rp-feats" aria-label="Keunggulan Ruang Pustaka">
                <div class="rp-feat"><span>📖</span>
                    <div><b>Baca langsung di platform</b>
                        <p>E-book dibaca di platform ini, bukan PDF.</p>
                    </div>
                </div>
                <div class="rp-feat"><span>🏷️</span>
                    <div><b>Harga terjangkau</b>
                        <p>Hemat lebih banyak dengan voucher e-book.</p>
                    </div>
                </div>
                <div class="rp-feat"><span>🕑</span>
                    <div><b>Akses kapan saja</b>
                        <p>Buka koleksimu di mana saja, kapan saja.</p>
                    </div>
                </div>
            </section>

            <!-- ==================== KATEGORI / FILTER GENRE ==================== -->
            @if(count($categories ?? []) > 0)
                <section class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-wine-700 rounded-full"></span>
                        <h2 class="text-sm font-semibold text-stone-500">Jelajahi Kategori Cepat</h2>
                    </div>
                    <div class="flex gap-2.5 overflow-x-auto no-scrollbar pb-1">
                        @foreach($categories as $category)
                            @if(count(data_get($category, 'books', [])) > 0)
                                <a href="#kategori-{{ data_get($category, 'id') }}"
                                    class="shrink-0 px-4 py-2 rounded-full text-xs font-semibold bg-white border border-stone-200/80 text-stone-700 rp-chip shadow-xs flex items-center gap-1.5">
                                    <span>{{ data_get($category, 'name', 'Kategori') }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Helper Blade Template Component/Snippet for Book Card to avoid repetition -->
            @php
                $renderBookCard = function ($book) {
                    $targetUrl = Auth::check() ? route('books.show', data_get($book, 'id', 1)) : route('login');
                    return '<div class="rp-card w-[180px] sm:w-[200px] shrink-0 flex flex-col justify-between group/card">
                                        <div>
                                            <a href="' . $targetUrl . '" class="rp-cover block w-full aspect-[3/4] bg-stone-100 rounded-r-xl rounded-l-sm overflow-hidden mb-3.5 relative">
                                                ' . (data_get($book, 'cover_image')
                        ? '<img src="' . asset('storage/' . data_get($book, 'cover_image')) . '" alt="' . data_get($book, 'title') . '" class="w-full h-full object-cover group-hover/card:scale-105 transition duration-500">'
                        : '<div class="w-full h-full flex flex-col items-center justify-center bg-stone-100 text-stone-300 p-2 text-center">
                                                        <svg class="w-12 h-12 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2zm-3 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-6 6h6v1H9v-1z"/></svg>
                                                        <span class="text-[10px] text-stone-400">Tidak ada sampul</span>
                                                       </div>') . '
                                            </a>
                                            <a href="' . $targetUrl . '" class="font-bold text-xs sm:text-sm text-stone-900 line-clamp-1 block hover:text-wine-700 transition" title="' . data_get($book, 'title') . '">
                                                ' . data_get($book, 'title', 'Judul Buku') . '
                                            </a>
                                            <p class="text-[11px] text-stone-500 flex items-center gap-1 mt-1 truncate">
                                                <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                <span class="truncate">' . data_get($book, 'author', 'Penulis Tidak Diketahui') . '</span>
                                            </p>
                                        </div>
                                        <div class="mt-3 flex items-center justify-between">
                                            <span class="rp-price font-extrabold text-xs sm:text-sm text-stone-900">
                                                Rp ' . number_format(data_get($book, 'price', 0), 0, ',', '.') . '
                                            </span>
                                            <a href="' . $targetUrl . '" class="rp-buy p-2.5 bg-wine-700 hover:bg-wine-800 border border-wine-700 rounded-xl text-white transition shadow-md hover:shadow-lg flex items-center justify-center" title="Detail / Beli Buku">
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
                        <div class="rp-bar"></div>
                        <h2 class="font-serif text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900">Buku
                            Populer</h2>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            @if(strtolower(Auth::user()->role ?? '') === 'admin' || (isset(Auth::user()->is_admin) && Auth::user()->is_admin))
                                <a href="{{ route('books.create') }}"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Buku
                                </a>
                            @endif
                        @endauth

                        <a href="{{ Auth::check() ? route('books.index') : route('login') }}"
                            class="rp-all text-xs sm:text-sm font-semibold text-wine-800 flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="relative group/slider">
                    @if(count($popularBooks ?? []) > 4)
                        <button onclick="scrollSlider('slider-popular', 'left')"
                            class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button onclick="scrollSlider('slider-popular', 'right')"
                            class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif

                    <div id="slider-popular" class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
                        @forelse($popularBooks ?? [] as $book)
                            {!! $renderBookCard($book) !!}
                        @empty
                            <div
                                class="w-full py-12 text-center bg-white rounded-3xl border border-dashed border-stone-300 text-stone-400 text-xs">
                                Belum ada buku populer saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- ==================== REKOMENDASI UNTUKMU ==================== -->
            @if(count($recommendedBooks ?? []) > 0)
                <section class="rp-rec space-y-4 p-6 sm:p-10">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="rp-bar"></div>
                            <div>
                                <h2 class="font-serif text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900">
                                    Rekomendasi Untukmu</h2>
                                <p class="text-xs text-stone-500">Dipilih berdasarkan minat pembaca lain</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative group/slider">
                        @if(count($recommendedBooks) > 4)
                            <button onclick="scrollSlider('slider-recommended', 'left')"
                                class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button onclick="scrollSlider('slider-recommended', 'right')"
                                class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @endif

                        <div id="slider-recommended"
                            class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
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
                            <div class="rp-bar rp-bar-g"></div>
                            <h2 class="font-serif text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900">Buku Baru
                            </h2>
                            <span
                                class="bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-extrabold px-3 py-0.5 rounded-full">&lt;
                                24 Jam</span>
                        </div>
                        <a href="{{ Auth::check() ? route('books.index') : route('login') }}"
                            class="rp-all text-xs sm:text-sm font-semibold text-wine-800 flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="relative group/slider">
                        @if(count($newBooks) > 4)
                            <button onclick="scrollSlider('slider-new', 'left')"
                                class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button onclick="scrollSlider('slider-new', 'right')"
                                class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
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
                                <div class="rp-bar"></div>
                                <h2 class="font-serif text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900">
                                    {{ data_get($category, 'name', 'Kategori') }}</h2>
                            </div>
                            <a href="{{ Auth::check() ? route('books.index') : route('login') }}"
                                class="rp-all text-xs sm:text-sm font-semibold text-wine-800 flex items-center gap-1">
                                Lihat Semua
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="relative group/slider">
                            @if(count(data_get($category, 'books', [])) > 4)
                                <button onclick="scrollSlider('slider-cat-{{ data_get($category, 'id') }}', 'left')"
                                    class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button onclick="scrollSlider('slider-cat-{{ data_get($category, 'id') }}', 'right')"
                                    class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif

                            <div id="slider-cat-{{ data_get($category, 'id') }}"
                                class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
                                @foreach(data_get($category, 'books', []) as $book)
                                    {!! $renderBookCard($book) !!}
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endforeach

            <!-- ==================== TESTIMONI / TRUST SECTION ==================== -->
            <section class="rp-trust mt-16 bg-white border p-8 sm:p-12 shadow-sm relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-wine-50 rounded-full blur-3xl pointer-events-none">
                </div>
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div>
                        <div
                            class="inline-flex items-center gap-2 bg-wine-50 text-wine-800 px-3 py-1.5 rounded-full text-xs font-bold mb-4 border border-wine-100">
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
                                <span class="text-sm text-stone-500 mt-1 block">Koleksi E-Book</span>
                            </div>
                            <div>
                                <span class="block text-3xl font-black text-gold-500 font-serif">10k+</span>
                                <span class="text-sm text-stone-500 mt-1 block">Pengguna Aktif</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 relative">
                        <div
                            class="bg-stone-50 p-5 rounded-2xl border border-stone-200 shadow-sm relative z-10 transform md:-translate-x-8 hover:-translate-y-1 transition duration-300">
                            <div class="flex items-center gap-1 mb-3">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                        </path>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-xs sm:text-sm text-stone-700 italic mb-4">"Sangat membantu untuk cari
                                referensi tugas kuliah. Aksesnya cepat dan harganya terjangkau pakai voucher!"</p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-wine-200 rounded-full flex items-center justify-center text-wine-800 font-bold text-xs">
                                    AD</div>
                                <div>
                                    <span class="block text-xs font-bold text-ink-900">Andi</span>
                                    <span class="block text-[10px] text-stone-500">Mahasiswa</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-wine-900 text-white p-5 rounded-2xl shadow-xl relative z-20 transform md:translate-x-4 hover:-translate-y-1 transition duration-300">
                            <div class="flex items-center gap-1 mb-3">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                        </path>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-xs sm:text-sm text-wine-50 italic mb-4">"Aplikasinya keren, bacanya nyaman
                                banget di mata. Lebih praktis daripada bawa buku fisik tebal-tebal."</p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-gold-400 rounded-full flex items-center justify-center text-ink-900 font-bold text-xs">
                                    SR</div>
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
    <footer
        class="rp-foot border-t border-wine-900/20 bg-gradient-to-r from-wine-900 via-ink-900 to-wine-900 mt-20 py-8 text-xs text-wine-200">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2.5 font-bold text-wine-50 text-sm">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka" class="h-7 w-7 object-contain">
                <span>Ruang Pustaka</span>
            </div>
            <p class="text-wine-300/80">&copy; {{ date('Y') }} Ruang Pustaka. Semua hak cipta dilindungi.</p>
        </div>
    </footer>

    <!-- GSAP 3.15+ (dibutuhkan untuk easeReverse) -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>

    <!-- Script Slider Horisontal & Salin Promo -->
    <script>
        function profileMenu() {
            return {
                open: false,
                tl: null,
                init() {
                    const root = this.$el;
                    const panel = root.querySelector('[data-menu-panel]');
                    const chev = root.querySelector('[data-menu-chev]');
                    const items = root.querySelectorAll('[data-menu-item]');
                    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    const EXIT_SPEED = 1.5; // 1 = sama dengan saat buka, makin besar makin cepat menutup

                    this.tl = gsap.timeline({ paused: true })
                        .from(panel, { autoAlpha: 0, yPercent: -10, scale: 0.6, duration: 0.8, transformOrigin: 'top right', ease: 'back.out(2)', easeReverse: 'power3.out' }, 0)
                        .to(chev, { rotation: 180, transformOrigin: '50% 50%', duration: 0.5, ease: 'back.out(2)', easeReverse: 'power2.out' }, 0)
                        .from(items, { opacity: 0, y: 6, duration: 0.32, ease: 'power2.out', easeReverse: true, stagger: 0.05 }, 0.18);

                    this.$watch('open', (isOpen) => {
                        if (isOpen) this.tl.timeScale(reduce ? 50 : 1).play();
                        else this.tl.timeScale(reduce ? 50 : EXIT_SPEED).reverse();
                    });
                }
            };
        }

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
            const scrollAmount = 3
            container.scrollBy({
                left: direction === 'left' ? -scrollAmount : scrollAmount,
                behavior: 'smooth'
            });
        }
    </script>
    <script>
        (function () {
            const nav = document.querySelector('.rp-nav'), bar = document.getElementById('rp-progress'), top = document.getElementById('rp-top');
            const onScroll = () => {
                const y = window.scrollY, h = document.documentElement.scrollHeight - innerHeight;
                bar.style.transform = 'scaleX(' + (h > 0 ? y / h : 0) + ')';
                nav && nav.classList.toggle('is-scrolled', y > 24);
                top.classList.toggle('on', y > 700);
            };
            addEventListener('scroll', onScroll, { passive: true }); onScroll();
            const inp = document.querySelector('nav input[type="text"]');
            if (inp) {
                const k = document.createElement('kbd'); k.className = 'rp-kbd'; k.textContent = '/';
                inp.parentElement.appendChild(k);
                const sync = () => { k.style.display = (document.activeElement === inp || inp.value) ? 'none' : ''; };
                inp.addEventListener('focus', sync); inp.addEventListener('blur', sync); inp.addEventListener('input', sync);
                addEventListener('keydown', e => {
                    if (e.key === '/' && !/INPUT|TEXTAREA/.test(document.activeElement.tagName)) { e.preventDefault(); inp.focus(); }
                });
            }
            const bk = document.getElementById('rp-book');
            if (bk) {
                const st = bk.parentElement;
                st.addEventListener('pointermove', e => {
                    const r = st.getBoundingClientRect();
                    bk.style.setProperty('--ry', (((e.clientX - r.left) / r.width - .5) * 30) + 'deg');
                    bk.style.setProperty('--rx', (-((e.clientY - r.top) / r.height - .5) * 22) + 'deg');
                });
                st.addEventListener('pointerleave', () => { bk.style.removeProperty('--ry'); bk.style.removeProperty('--rx'); });
            }
        })();
    </script>
</body>

</html>