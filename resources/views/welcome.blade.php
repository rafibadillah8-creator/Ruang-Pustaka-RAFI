<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Pustaka - Perpustakaan & Arsip Digital</title>
    {{-- [BARU] Terapkan tema tersimpan sebelum halaman tampil (cegah berkedip) --}}
    <script>(function(){try{var t=localStorage.getItem('rp-theme');if(!t)t=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';document.documentElement.dataset.theme=t}catch(e){}})()</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        [x-cloak] { display: none !important }
        .font-serif { font-family: 'Fraunces', Georgia, serif }
        .font-sans, body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif }
        body { background-image: radial-gradient(900px 500px at 100% 0, rgba(184, 134, 59, .10), transparent 60%), radial-gradient(700px 500px at 0 40%, rgba(111, 127, 94, .08), transparent 60%) }
        a:focus-visible, button:focus-visible, input:focus-visible { outline: 2px solid #C79A4B; outline-offset: 3px }
        #rp-progress { position: fixed; top: 0; left: 0; height: 3px; width: 100%; transform: scaleX(0); transform-origin: left; background: linear-gradient(90deg, #B8863B, #E3BE7C); z-index: 60 }
        .rp-nav { backdrop-filter: blur(14px); transition: padding .25s, box-shadow .25s }
        .rp-nav.is-scrolled { padding-top: .5rem; padding-bottom: .5rem; box-shadow: 0 10px 30px -12px rgba(0, 0, 0, .45) }
        .rp-hero::after { content: ""; position: absolute; inset: 0; pointer-events: none; z-index: 5; background-image: radial-gradient(rgba(255, 255, 255, .07) 1px, transparent 1px); background-size: 3px 3px; opacity: .55 }
        .rp-rise>* { animation: rpIn .7s cubic-bezier(.2, .7, .2, 1) both }
        .rp-rise>:nth-child(2) { animation-delay: .1s }
        .rp-rise>:nth-child(3) { animation-delay: .2s }
        .rp-rise>:nth-child(4) { animation-delay: .3s }
        @keyframes rpIn { from { opacity: 0; transform: translateY(14px) } }
        .rp-book { transform: perspective(900px) rotateX(var(--rx, 4deg)) rotateY(var(--ry, -12deg)); transform-style: preserve-3d; transition: transform .25s ease-out }
        .rp-card { transition: transform .3s cubic-bezier(.2, .7, .2, 1), box-shadow .3s, border-color .3s }
        .rp-card:hover { transform: translateY(-4px); border-color: #C79A4B; box-shadow: 0 22px 34px -22px rgba(46, 29, 25, .55) }
        .rp-cover { transform-origin: left center; transition: transform .35s cubic-bezier(.2, .7, .2, 1), box-shadow .35s; box-shadow: 0 10px 18px -12px rgba(40, 20, 10, .6) }
        .rp-cover::before { content: ""; position: absolute; inset: 0 auto 0 0; width: 9px; z-index: 2; pointer-events: none; background: linear-gradient(90deg, rgba(0, 0, 0, .28), rgba(255, 255, 255, .14)) }
        .rp-card:hover .rp-cover { transform: perspective(700px) rotateY(-12deg); box-shadow: 12px 18px 24px -14px rgba(40, 20, 10, .55) }
        #rp-top { position: fixed; right: 20px; bottom: calc(20px + env(safe-area-inset-bottom, 0px)); z-index: 50; width: 44px; height: 44px; border-radius: 50%; background: #2A1A17; color: #E3BE7C; border: 1px solid rgba(227, 190, 124, .4); display: grid; place-items: center; opacity: 0; transform: translateY(12px); pointer-events: none; transition: .3s; cursor: pointer }
        #rp-top.on { opacity: 1; transform: none; pointer-events: auto }
        .rp-kbd { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 11px; line-height: 18px; padding: 0 6px; border: 1px solid #d6d3d1; border-radius: 6px; color: #78716c; pointer-events: none; background: #fafaf9 }
        .rp-nav { top: 12px !important; margin: 12px auto 0; width: min(1200px, calc(100% - 24px)); border-radius: 999px; border: 1px solid rgba(227, 190, 124, .2) !important }
        .rp-hero { border-radius: 2rem !important }
        .rp-hero::before { content: ""; position: absolute; right: -130px; top: -130px; width: 420px; height: 420px; border-radius: 50%; border: 1px solid rgba(227, 190, 124, .2); box-shadow: 0 0 0 60px rgba(227, 190, 124, .04), 0 0 0 120px rgba(227, 190, 124, .025); pointer-events: none; z-index: 1 }
        .rp-hero .bg-gold-500 { border-radius: 999px !important }
        .rp-hero [class*="bg-black/30"] { border-radius: 999px !important }
        .rp-ticket { border-radius: 22px !important }
        .rp-ticket::before, .rp-ticket::after { content: ""; position: absolute; top: 62%; width: 22px; height: 22px; border-radius: 50%; background: #2d1d19 }
        .rp-ticket::before { left: -11px }
        .rp-ticket::after { right: -11px }
        .rp-ticket .border-t { border-top-style: dashed !important }
        .rp-feats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px }
        .rp-feat { display: flex; gap: 14px; align-items: flex-start; background: #FFFCF5; border: 1px solid #E4D9C5; border-radius: 22px; padding: 18px 20px; transition: transform .3s, border-color .3s, box-shadow .3s }
        .rp-feat:hover { transform: translateY(-3px); border-color: #C79A4B; box-shadow: 0 18px 30px -22px rgba(46, 29, 25, .5) }
        .rp-feat>span { flex: none; width: 44px; height: 44px; border-radius: 14px; background: #F0E2C4; display: grid; place-items: center; font-size: 20px }
        .rp-feat b { display: block; font-family: 'Fraunces', Georgia, serif; font-size: 17px; line-height: 1.25; color: #2B1D18 }
        .rp-feat p { font-size: 13px; color: #75655B; margin-top: 4px }
        .rp-chip { transition: transform .2s, background .2s, color .2s, border-color .2s }
        .rp-chip:hover { background: #2A1A17; color: #F3EADB; border-color: #2A1A17; transform: translateY(-2px) }
        .rp-bar { width: 26px; height: 4px; border-radius: 99px; background: linear-gradient(90deg, #B8863B, #E3BE7C) }
        .rp-bar-g { background: linear-gradient(90deg, #6F7F5E, #A9B88F) }
        .rp-all { padding: 7px 16px; border: 1px solid #E4D9C5; border-radius: 999px; background: #FFFCF5; transition: .2s }
        .rp-all:hover { border-color: #B8863B; background: #F0E2C4; text-decoration: none !important }
        .rp-price { background: #F0E2C4; border-radius: 999px; padding: 4px 12px }
        .rp-card .rp-buy { border-radius: 999px !important; transition: transform .2s, background .2s }
        .rp-card:hover .rp-buy { transform: scale(1.08) }
        .rp-rec { background: linear-gradient(135deg, rgba(111, 127, 94, .14), rgba(255, 252, 245, .9)); border: 1px solid #E4D9C5; border-radius: 2rem }
        .rp-trust { border-radius: 2rem !important; border-color: #E4D9C5 !important }
        .rp-foot { border-radius: 2.5rem 2.5rem 0 0 }
        @media(max-width:768px) {
            .rp-feats { grid-template-columns: 1fr }
            .rp-nav { border-radius: 28px }
        }
        @media(prefers-reduced-motion:reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; scroll-behavior: auto !important }
        }

        /* ===== [BARU] Ruang Baca Malam: token, mode terang/gelap ===== */
        :root{--rp-bg:#F6EFE2;--rp-surface:#FCF8EF;--rp-surface2:#F0E6D2;--rp-ink:#2B211B;--rp-soft:#6B5B4F;--rp-line:#E3D6BF;--rp-nav:#2A1A17;--rp-link:#8a5f1f}
        html[data-theme="dark"]{--rp-bg:#17120F;--rp-surface:#221A16;--rp-surface2:#2C221C;--rp-ink:#EADFCB;--rp-soft:#A99A88;--rp-line:#3A2E27;--rp-nav:#120D0B;--rp-link:#D2A65A;color-scheme:dark}
        @view-transition{navigation:auto}
        body.bg-parchment{background-color:var(--rp-bg);color:var(--rp-ink);transition:background-color .3s,color .3s;
         background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='2'/><feColorMatrix values='0 0 0 0 .4 0 0 0 0 .3 0 0 0 0 .2 0 0 0 .08 0'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>")}

        /* navbar rata & solid (tidak melayang menimpa konten) */
        .rp-nav{top:0!important;margin:0!important;width:100%!important;border-radius:0!important;border-width:0 0 1px 0!important;background-image:none!important;background-color:var(--rp-nav)!important}

        /* tombol tema */
        .rp-theme{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;color:#E3BE7C;border:1px solid rgba(227,190,124,.35);background:transparent;cursor:pointer;transition:background .2s,transform .2s}
        .rp-theme:hover{background:rgba(227,190,124,.15);transform:rotate(12deg)}
        .rp-theme .i-sun{display:none}
        html[data-theme="dark"] .rp-theme .i-sun{display:block}
        html[data-theme="dark"] .rp-theme .i-moon{display:none}

        /* rak kategori */
        .rp-shelf{display:flex;align-items:flex-end;gap:7px;padding:22px 18px 0;border-bottom:12px solid #6b4a35;overflow-x:auto;border-radius:14px 14px 0 0;background:linear-gradient(var(--rp-surface),transparent)}
        .rp-spine{flex:none;width:48px;display:grid;place-items:center;border-radius:3px 3px 0 0;color:#fff;font-size:13px;font-weight:600;text-decoration:none;transition:transform .3s cubic-bezier(.34,1.56,.64,1)}
        .rp-spine span{writing-mode:vertical-rl;transform:rotate(180deg);letter-spacing:.03em;padding:12px 0}
        .rp-spine:hover,.rp-spine:focus-visible{transform:translateY(-12px) rotate(-1.5deg)}

        /* kartu lanjutkan membaca (di hero) */
        .rp-resume{display:flex;align-items:center;gap:14px;max-width:440px;margin:6px auto 0;padding:14px 16px;border-radius:18px;background:rgba(0,0,0,.28);border:1px solid rgba(255,255,255,.12);text-align:left;text-decoration:none;transition:transform .2s}
        .rp-resume:hover{transform:translateY(-2px)}
        .rp-resume i{display:block;height:5px;border-radius:9px;background:rgba(255,255,255,.2);overflow:hidden;margin:6px 0 0}
        .rp-resume i b{display:block;height:100%;background:#E3BE7C}
        @media(min-width:768px){.rp-resume{margin-left:0}}

        /* mode gelap: hanya area konten & navbar, hero tidak ikut */
        html[data-theme="dark"] :is(main,nav) :is(.bg-white,.bg-white\/95):not(.rp-hero *){background-color:var(--rp-surface)}
        html[data-theme="dark"] :is(main,nav) :is(.bg-stone-50,.bg-stone-100,.bg-wine-50):not(.rp-hero *){background-color:var(--rp-surface2)}
        html[data-theme="dark"] :is(main,nav) :is(.border-stone-100,.border-stone-200,.border-stone-300,.border-stone-200\/80):not(.rp-hero *){border-color:var(--rp-line)}
        html[data-theme="dark"] :is(main,nav) :is(.text-stone-900,.text-stone-800,.text-stone-700,.text-ink-900):not(.rp-hero *):not(.bg-gold-500):not(.bg-gold-400){color:var(--rp-ink)}
        html[data-theme="dark"] :is(main,nav) :is(.text-stone-600,.text-stone-500,.text-stone-400,.text-stone-300):not(.rp-hero *){color:var(--rp-soft)}
        html[data-theme="dark"] :is(main,nav) .text-wine-800:not(.rp-hero *){color:var(--rp-link)}
        html[data-theme="dark"] nav .hover\:bg-wine-50\/60:hover{background-color:var(--rp-surface2)}
        html[data-theme="dark"] .rp-feat{background:var(--rp-surface);border-color:var(--rp-line)}
        html[data-theme="dark"] .rp-feat b{color:var(--rp-ink)}
        html[data-theme="dark"] .rp-feat p{color:var(--rp-soft)}
        html[data-theme="dark"] .rp-feat>span{background:var(--rp-surface2)}
        html[data-theme="dark"] .rp-all{background:var(--rp-surface);border-color:var(--rp-line)}
        html[data-theme="dark"] .rp-all:hover{background:var(--rp-surface2)}
        html[data-theme="dark"] .rp-price{background:var(--rp-surface2)}
        html[data-theme="dark"] .rp-rec{background:linear-gradient(135deg,rgba(111,127,94,.18),var(--rp-surface));border-color:var(--rp-line)}
        html[data-theme="dark"] .rp-trust{border-color:var(--rp-line)!important}
        html[data-theme="dark"] .rp-kbd{background:var(--rp-surface2);border-color:var(--rp-line);color:var(--rp-soft)}

        /* ===== [BARU] Kartu buku "ceruk rak": sampul berdiri di ceruk melengkung, lampu baca menyala saat disentuh ===== */
        .rp-card:hover{transform:none;box-shadow:none;border-color:transparent}
        .rp-niche{position:relative;display:block;height:262px;text-decoration:none;-webkit-tap-highlight-color:transparent}
        .rp-arch{position:absolute;z-index:0;inset:34px 0 0 0;overflow:hidden;border-radius:50% 50% 20px 20px / 92px 92px 20px 20px;
          background:radial-gradient(120% 80% at 50% 28%,color-mix(in srgb,var(--tint) 12%,#FFF1CF) 0%,color-mix(in srgb,var(--tint) 34%,var(--rp-surface)) 72%);
          border:1px solid color-mix(in srgb,var(--tint) 40%,var(--rp-line))}
        html[data-theme="dark"] .rp-arch{background:radial-gradient(120% 80% at 50% 28%,color-mix(in srgb,var(--tint) 30%,#3a2d22) 0%,color-mix(in srgb,var(--tint) 22%,var(--rp-surface)) 72%)}
        .rp-arch::before{content:"";position:absolute;left:50%;top:-10%;width:130%;height:70%;transform:translateX(-50%);background:radial-gradient(closest-side,rgba(255,214,140,.6),transparent);opacity:.3;transition:opacity .5s}
        .rp-card:hover .rp-arch::before,.rp-niche:focus-visible .rp-arch::before{opacity:1}
        .rp-ledge{position:absolute;z-index:1;left:1px;right:1px;bottom:1px;height:28px;border-radius:0 0 19px 19px;background:linear-gradient(180deg,#8a6a4e,#5f4130);box-shadow:inset 0 2px 0 rgba(255,255,255,.2)}
        .rp-niche::after{content:"";position:absolute;z-index:2;left:50%;bottom:22px;width:58%;height:14px;transform:translateX(-50%);background:radial-gradient(closest-side,rgba(20,10,0,.5),transparent);transition:transform .45s,opacity .45s;pointer-events:none}
        .rp-card:hover .rp-niche::after{transform:translateX(-50%) scale(.75);opacity:.5}
        .rp-niche .rp-cover{position:absolute;z-index:3;left:50%;bottom:28px;width:66%;aspect-ratio:3/4;transform:translateX(-50%);transform-origin:50% 100%;overflow:hidden;border-radius:3px 7px 7px 3px;background:var(--tint);box-shadow:0 14px 18px -12px rgba(30,15,5,.7);transition:transform .45s cubic-bezier(.2,.8,.2,1),box-shadow .45s}
        .rp-card:hover .rp-niche .rp-cover{transform:perspective(700px) translateX(-50%) translateY(-12px) rotateY(-9deg);box-shadow:10px 22px 24px -14px rgba(30,15,5,.65)}
        .rp-fb{position:absolute;inset:0;display:flex;align-items:flex-end;padding:12px 10px;color:#fff;font:700 14px/1.15 'Fraunces',Georgia,serif;background:linear-gradient(160deg,var(--tint),color-mix(in srgb,var(--tint) 55%,#000))}
        .rp-rib{position:absolute;z-index:4;top:0;right:16px;width:22px;height:46px;padding-top:8px;display:grid;place-items:start center;font-style:normal;font-size:12px;font-weight:700;color:#fff;background:#3F5B4A;clip-path:polygon(0 0,100% 0,100% 100%,50% 82%,0 100%)}
        .rp-meta{display:flex;flex-direction:column;flex:1;padding:12px 4px 0}
        .rp-title{font-size:15px;font-weight:700;line-height:1.25;min-height:2.5em;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;text-decoration:none;transition:color .2s}
        .rp-card .rp-title:hover{color:var(--rp-link)}
        .rp-by{font-size:12px;margin:4px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .rp-foot{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:auto;padding-top:12px}
        .rp-free{background:#DDE8DD;color:#2f5a3c}
        html[data-theme="dark"] .rp-price.rp-free{background:rgba(63,91,74,.4);color:#9dc2aa}

        /* ===== [BARU] Pop-up detail buku ===== */
        .rp-modal{border:0;padding:0;margin:auto;width:min(860px,calc(100% - 24px));max-height:calc(100dvh - 24px);background:var(--rp-surface);color:var(--rp-ink);border-radius:26px;box-shadow:0 40px 80px -30px rgba(0,0,0,.65);overflow:hidden}
        .rp-modal::backdrop{background:rgba(23,18,15,.64);backdrop-filter:blur(3px)}
        .rp-modal[open]{animation:rpPop .28s cubic-bezier(.2,.8,.2,1)}
        .rp-modal[open]::backdrop{animation:rpFade .25s ease-out}
        @keyframes rpPop{from{opacity:0;transform:translateY(14px) scale(.96)}}
        @keyframes rpFade{from{opacity:0}}
        .rp-m-x{position:absolute;top:14px;right:14px;z-index:3;width:40px;height:40px;border-radius:50%;border:1px solid var(--rp-line);background:var(--rp-surface);color:var(--rp-ink);font-size:22px;line-height:1;cursor:pointer;transition:transform .2s,background .2s}
        .rp-m-x:hover{transform:rotate(90deg);background:var(--rp-surface2)}
        .rp-m-body{display:grid;grid-template-columns:250px 1fr;gap:28px;padding:30px;max-height:calc(100dvh - 24px);overflow:auto}
        .rp-m-cover{align-self:start;perspective:800px}
        .rp-m-cover .cv{position:relative;aspect-ratio:3/4;border-radius:4px 10px 10px 4px;overflow:hidden;background:linear-gradient(160deg,#3F5B4A,#223329);box-shadow:0 18px 30px -14px rgba(40,20,10,.65);transform:rotateY(-10deg);transition:transform .5s cubic-bezier(.2,.8,.2,1)}
        .rp-m-cover .cv:hover{transform:rotateY(-2deg)}
        .rp-m-cover .cv::before{content:"";position:absolute;inset:0 auto 0 0;width:9px;z-index:2;pointer-events:none;background:linear-gradient(90deg,rgba(0,0,0,.28),rgba(255,255,255,.14))}
        .rp-m-cover .cv img{width:100%;height:100%;object-fit:cover;display:block}
        .rp-m-cover .fb{position:absolute;inset:0;display:flex;align-items:flex-end;padding:18px 16px;color:#fff;font:700 20px/1.15 'Fraunces',Georgia,serif}
        .rp-m-cats{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;padding-right:48px}
        .rp-m-cats span{font-size:11px;font-weight:600;padding:4px 10px;border-radius:999px;background:var(--rp-surface2);border:1px solid var(--rp-line)}
        .rp-m-info h2{font:700 clamp(24px,3.4vw,32px)/1.12 'Fraunces',Georgia,serif;margin:0;padding-right:40px}
        .rp-m-author{margin:8px 0 0;color:var(--rp-soft);font-size:14px;white-space:pre-line}
        .rp-m-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:20px 0}
        .rp-m-stats div{background:var(--rp-surface2);border-radius:14px;padding:11px 14px}
        .rp-m-stats small{display:block;font-size:11px;color:var(--rp-soft)}
        .rp-m-stats b{font-size:15px}
        .rp-m-desc{font-size:14px;line-height:1.75;color:var(--rp-soft);margin:0 0 22px;max-height:9.5em;overflow:auto}
        .rp-m-actions{display:flex;gap:10px;flex-wrap:wrap}
        .rp-m-cta{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 24px;border-radius:999px;font-weight:700;font-size:14px;text-decoration:none;color:#2A1A17;background:linear-gradient(90deg,#B8863B,#E3BE7C);transition:transform .2s,box-shadow .2s}
        .rp-m-cta:hover{transform:translateY(-2px);box-shadow:0 12px 22px -12px rgba(184,134,59,.8)}
        .rp-m-sec{min-height:46px;padding:0 20px;border-radius:999px;border:1px solid var(--rp-line);background:none;color:var(--rp-ink);font-weight:600;font-size:14px;cursor:pointer}
        @media(max-width:640px){.rp-m-body{grid-template-columns:1fr;gap:18px;padding:22px 18px}.rp-m-cover{width:150px;margin:0 auto}.rp-m-stats b{font-size:13px}}
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
                {{-- [BARU] Tombol tema terang/gelap --}}
                <button type="button" id="rp-theme" class="rp-theme" aria-label="Ganti tema terang atau gelap" title="Ganti tema">
                    <svg class="i-moon w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z"/></svg>
                    <svg class="i-sun w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4l1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4m11.4-11.4l1.4-1.4"/></svg>
                </button>

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
                            class="absolute right-0 top-full mt-2 w-56 bg-ink-900 border border-wine-800 rounded-2xl shadow-2xl py-2 z-50">

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

                            {{-- [BARU] Lanjutkan membaca (hanya muncul jika $continueReading dikirim dari route) --}}
                            @isset($continueReading)
                                <a href="{{ route('books.show', $continueReading->id) }}" class="rp-resume">
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-[11px] text-wine-200">Lanjutkan membaca</span>
                                        <span class="block font-serif font-bold text-white truncate">{{ $continueReading->title }}</span>
                                        <i><b style="width: {{ (int) ($continueReading->progress ?? 0) }}%"></b></i>
                                    </span>
                                    <span class="text-xs font-bold text-gold-400">Lanjut</span>
                                </a>
                            @endisset
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

            <!-- ==================== KATEGORI / FILTER GENRE ==================== -->
            @php
                $safeCategories = $categories ?? [];
            @endphp
            @if(count($safeCategories) > 0)
                <section class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-wine-700 rounded-full"></span>
                        <h2 class="text-sm font-semibold text-stone-500">Rak Kategori</h2>
                    </div>
                    {{-- [BARU] Kategori sebagai punggung buku di rak (tautan #kategori-ID tetap sama) --}}
                    <div class="rp-shelf no-scrollbar">
                        @php
                            $spineColors = ['#3F5B4A', '#B5472F', '#3B2A22', '#B8893B', '#5C6B7A', '#7a3f55', '#2f5560', '#6b4a35'];
                            $spineHeights = [168, 150, 180, 158, 172, 146];
                        @endphp
                        @foreach($safeCategories as $category)
                            @php
                                $catBooks = is_array($category) ? ($category['books'] ?? []) : ($category->books ?? []);
                                $catId = is_array($category) ? ($category['id'] ?? 1) : $category->id;
                                $catName = is_array($category) ? ($category['name'] ?? 'Kategori') : $category->name;
                            @endphp
                            @if(count($catBooks) > 0)
                                <a href="#kategori-{{ $catId }}" class="rp-spine"
                                    style="min-height: {{ $spineHeights[$loop->index % 6] }}px; background: {{ $spineColors[$loop->index % 8] }}"
                                    aria-label="Kategori {{ $catName }}"><span>{{ $catName }}</span></a>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Helper Blade Template Component/Snippet for Book Card to avoid repetition -->
            @php
                $ownedIds = isset($purchasedBookIds) ? (array) $purchasedBookIds : [];
                $tints = ['#3F5B4A', '#B5472F', '#6b4a35', '#B8893B', '#5C6B7A', '#7a3f55', '#2f5560'];
                $renderBookCard = function ($book) use ($ownedIds, $tints) {
                    $targetUrl = Auth::check() ? route('books.show', data_get($book, 'id', 1)) : route('login');

                    // data untuk pop-up detail buku (tanpa memicu query tambahan)
                    $attrs = is_array($book) ? $book : (method_exists($book, 'getAttributes') ? $book->getAttributes() : (array) $book);
                    $pages = null;
                    foreach (['total_pages', 'page_count', 'pages_count', 'jumlah_halaman', 'pages', 'halaman'] as $pk) {
                        if (isset($attrs[$pk]) && is_numeric($attrs[$pk])) { $pages = (int) $attrs[$pk]; break; }
                    }
                    $bid = data_get($book, 'id', 1);
                    $owned = in_array($bid, $ownedIds);
                    $cats = [];
                    if (is_object($book) && method_exists($book, 'relationLoaded') && $book->relationLoaded('categories')) {
                        $cats = $book->categories->pluck('name')->take(3)->all();
                    }
                    $coverFile = data_get($book, 'cover_image');
                    $hasCover = $coverFile && $coverFile !== 'default_cover.jpg';
                    $price = (float) data_get($book, 'price', 0);
                    $payload = [
                        'title'     => data_get($book, 'title', 'Judul Buku'),
                        'author'    => data_get($book, 'author', 'Penulis Tidak Diketahui'),
                        'publisher' => data_get($book, 'publisher'),
                        'price'     => $price,
                        'pages'     => $pages,
                        'desc'      => \Illuminate\Support\Str::limit(strip_tags((string) data_get($book, 'description', '')), 320),
                        'cover'     => $hasCover ? asset('storage/' . $coverFile) : null,
                        'cats'      => $cats,
                        'owned'     => $owned,
                        'url'       => $owned ? route('books.read', $bid) : $targetUrl,
                        'cta'       => $owned ? 'Baca Buku' : (Auth::check() ? 'Lihat Detail & Beli' : 'Masuk untuk Membeli'),
                    ];

                    // tampilan kartu "ceruk rak"
                    $title  = e(data_get($book, 'title', 'Judul Buku'));
                    $author = e(data_get($book, 'author', 'Penulis Tidak Diketahui'));
                    $tint   = $tints[((int) $bid) % count($tints)];
                    $coverHtml = $hasCover
                        ? '<img src="' . e(asset('storage/' . $coverFile)) . '" alt="Sampul ' . $title . '" loading="lazy" class="w-full h-full object-cover">'
                        : '<span class="rp-fb">' . $title . '</span>';
                    $priceHtml = $price > 0
                        ? '<span class="rp-price font-extrabold text-xs sm:text-sm">Rp ' . number_format($price, 0, ',', '.') . '</span>'
                        : '<span class="rp-price rp-free font-extrabold text-xs sm:text-sm">Gratis</span>';
                    $ribbon = $owned ? '<i class="rp-rib" aria-label="Sudah dimiliki">&#10003;</i>' : '';

                    return '<div class="rp-card w-[180px] sm:w-[200px] shrink-0 flex flex-col group/card" style="--tint:' . $tint . '" data-book="' . e(json_encode($payload, JSON_UNESCAPED_UNICODE)) . '">'
                        . '<a href="' . $targetUrl . '" class="rp-niche" aria-label="' . $title . '">'
                            . '<span class="rp-arch" aria-hidden="true"></span>'
                            . '<span class="rp-ledge" aria-hidden="true"></span>'
                            . '<span class="rp-cover">' . $coverHtml . '</span>'
                            . $ribbon
                        . '</a>'
                        . '<div class="rp-meta">'
                            . '<a href="' . $targetUrl . '" class="rp-title font-serif text-stone-900 block" title="' . $title . '">' . $title . '</a>'
                            . '<p class="rp-by text-stone-500">' . $author . '</p>'
                            . '<div class="rp-foot">' . $priceHtml
                                . '<a href="' . $targetUrl . '" class="rp-buy p-2.5 bg-wine-700 hover:bg-wine-800 border border-wine-700 rounded-xl text-white transition shadow-md hover:shadow-lg flex items-center justify-center" title="Lihat detail buku">'
                                . '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
                                . '</a>'
                            . '</div>'
                        . '</div>'
                    . '</div>';
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

                @php
                    $popBooks = $popularBooks ?? [];
                @endphp
                <div class="relative group/slider">
                    @if(count($popBooks) > 4)
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
                        @forelse($popBooks as $book)
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
            @php
                $recBooks = $recommendedBooks ?? [];
            @endphp
            @if(count($recBooks) > 0)
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
                        @if(count($recBooks) > 4)
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
                            @foreach($recBooks as $book)
                                {!! $renderBookCard($book) !!}
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <!-- ==================== 2. BUKU BARU (< 24 JAM) ==================== -->
            @php
                $nBooks = $newBooks ?? [];
            @endphp
            @if(count($nBooks) > 0)
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
                        @if(count($nBooks) > 4)
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
                            @foreach($nBooks as $book)
                                {!! $renderBookCard($book) !!}
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <!-- ==================== 3. KATEGORI BUKU ==================== -->
            @php
                $allCats = $categories ?? [];
            @endphp
            @if(count($allCats) > 0)
                @foreach($allCats as $category)
                    @php
                        $catBooks = is_array($category) ? ($category['books'] ?? []) : ($category->books ?? []);
                        $catId = is_array($category) ? ($category['id'] ?? 1) : $category->id;
                        $catName = is_array($category) ? ($category['name'] ?? 'Kategori') : $category->name;
                    @endphp
                    @if(count($catBooks) > 0)
                        <section id="kategori-{{ $catId }}" class="space-y-4 scroll-mt-28">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2.5">
                                    <div class="rp-bar"></div>
                                    <h2 class="font-serif text-2xl sm:text-3xl font-extrabold tracking-tight text-ink-900">
                                        {{ $catName }}
                                    </h2>
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
                                @if(count($catBooks) > 4)
                                    <button onclick="scrollSlider('slider-cat-{{ $catId }}', 'left')"
                                        class="absolute -left-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button onclick="scrollSlider('slider-cat-{{ $catId }}', 'right')"
                                        class="absolute -right-3 top-1/2 -translate-y-1/2 z-20 bg-white/95 backdrop-blur border border-stone-200 shadow-xl p-2.5 rounded-full hover:bg-white text-stone-700 transition hidden group-hover/slider:flex items-center justify-center cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif

                                <div id="slider-cat-{{ $catId }}"
                                    class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar py-2 px-1">
                                    @foreach($catBooks as $book)
                                        {!! $renderBookCard($book) !!}
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    @endif
                @endforeach
            @endif

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

    {{-- [BARU] Pop-up detail buku (diisi lewat JavaScript dari data-book pada kartu) --}}
    <dialog class="rp-modal" id="rp-modal" aria-labelledby="rp-m-title">
        <button type="button" class="rp-m-x" id="rp-m-x" aria-label="Tutup detail buku">&times;</button>
        <div class="rp-m-body">
            <div class="rp-m-cover"><div class="cv" id="rp-m-cv"></div></div>
            <div class="rp-m-info">
                <div class="rp-m-cats" id="rp-m-cats"></div>
                <h2 id="rp-m-title"></h2>
                <p class="rp-m-author" id="rp-m-author"></p>
                <div class="rp-m-stats">
                    <div><small>Harga</small><b id="rp-m-price"></b></div>
                    <div><small>Total halaman</small><b id="rp-m-pages"></b></div>
                    <div><small>Format</small><b>E-Book Digital</b></div>
                </div>
                <p class="rp-m-desc" id="rp-m-desc"></p>
                <div class="rp-m-actions">
                    <a class="rp-m-cta" id="rp-m-cta" href="#"></a>
                    <button type="button" class="rp-m-sec" id="rp-m-close2">Tutup</button>
                </div>
            </div>
        </div>
    </dialog>

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
                    const EXIT_SPEED = 1.5;

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
            const scrollAmount = 300;
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

    {{-- [BARU] Tombol tema terang/gelap --}}
    <script>
        document.getElementById('rp-theme')?.addEventListener('click', function () {
            var r = document.documentElement, n = r.dataset.theme === 'dark' ? 'light' : 'dark';
            r.dataset.theme = n;
            try { localStorage.setItem('rp-theme', n); } catch (e) {}
        });
    </script>

    {{-- [BARU] Logika pop-up detail buku --}}
    <script>
    (function () {
        var dlg = document.getElementById('rp-modal');
        if (!dlg || !dlg.showModal) return; // browser lama: kartu tetap berfungsi sebagai tautan biasa
        var $ = function (id) { return document.getElementById(id); };
        var fmt = function (n) { return new Intl.NumberFormat('id-ID').format(n); };

        function fill(b) {
            $('rp-m-title').textContent = b.title;
            $('rp-m-author').textContent = 'Penulis: ' + b.author + (b.publisher ? '\nPenerbit: ' + b.publisher : '');
            $('rp-m-price').textContent = b.price > 0 ? 'Rp ' + fmt(b.price) : 'GRATIS';
            $('rp-m-pages').textContent = b.pages ? fmt(b.pages) + ' halaman' : '-';
            var d = $('rp-m-desc'); d.textContent = b.desc || ''; d.style.display = b.desc ? '' : 'none';
            var cats = $('rp-m-cats'); cats.innerHTML = '';
            (b.cats || []).forEach(function (c) { var s = document.createElement('span'); s.textContent = c; cats.appendChild(s); });
            var cv = $('rp-m-cv'); cv.innerHTML = '';
            if (b.cover) { var im = new Image(); im.src = b.cover; im.alt = 'Sampul ' + b.title; cv.appendChild(im); }
            else { var f = document.createElement('div'); f.className = 'fb'; f.textContent = b.title; cv.appendChild(f); }
            var cta = $('rp-m-cta'); cta.href = b.url; cta.textContent = b.cta;
        }

        var lastCard = null;
        document.addEventListener('click', function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return; // Ctrl/Cmd+klik tetap buka halaman biasa
            var card = e.target.closest('.rp-card[data-book]');
            if (!card) return;
            var b; try { b = JSON.parse(card.dataset.book); } catch (x) { return; }
            e.preventDefault();
            lastCard = card; fill(b);
            document.documentElement.style.overflow = 'hidden';
            dlg.showModal();
        });

        function close() { dlg.close(); }
        $('rp-m-x').addEventListener('click', close);
        $('rp-m-close2').addEventListener('click', close);
        dlg.addEventListener('click', function (e) { if (e.target === dlg) close(); }); // klik area gelap
        dlg.addEventListener('close', function () {
            document.documentElement.style.overflow = '';
            var a = lastCard && lastCard.querySelector('a'); if (a) a.focus({ preventScroll: true });
        });
    })();
    </script>
</body>

</html>