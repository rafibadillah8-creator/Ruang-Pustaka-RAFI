<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Aman: {{ $book->title ?? 'E-Book' }} - Ruang Pustaka</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Palet warna disamakan dengan tema situs (wine / gold / ink / parchment) -->
    <style type="text/tailwindcss">
        @theme {
            --color-parchment: #FAF6EE;

            --color-ink-900: #1C1410;
            --color-ink-800: #2A1E18;

            --color-wine-50:  #FBF3F5;
            --color-wine-100: #F3E1E6;
            --color-wine-200: #E3C0CA;
            --color-wine-300: #CC93A3;
            --color-wine-400: #A85E74;
            --color-wine-500: #863F56;
            --color-wine-600: #6B2C41;
            --color-wine-700: #551F32;
            --color-wine-800: #401627;
            --color-wine-900: #2E0F1B;

            --color-gold-50:  #FCF7E8;
            --color-gold-100: #F7EBC3;
            --color-gold-200: #EFD888;
            --color-gold-300: #E4C158;
            --color-gold-400: #D9AE3E;
            --color-gold-500: #C1922A;
            --color-gold-600: #9C7420;
        }
    </style>

    <!-- Library PDF.js untuk merender halaman PDF ke Canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .secure-overlay {
            position: absolute;
            inset: 0;
            z-index: 20;
            background: transparent;
        }
        .watermark {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: rgba(46, 15, 27, 0.05);
            transform: rotate(-25deg);
            z-index: 10;
        }
    </style>
</head>

<body class="bg-parchment text-ink-900 min-h-screen pb-16 antialiased" oncontextmenu="return false;">

    <!-- Navbar Konsisten -->
    <nav class="border-b border-wine-900 bg-gradient-to-r from-wine-900 via-ink-900 to-wine-900 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-md">
        <a href="{{ url('/') }}" class="text-xl font-bold text-gold-400 hover:text-gold-300 flex items-center gap-2 transition">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span class="font-serif">Ruang Pustaka</span>
        </a>

        <div class="flex items-center gap-4">
            <span class="text-sm text-wine-100 hidden md:inline">Selamat membaca, <strong class="text-gold-400">{{ Auth::user()->name ?? 'User' }}</strong></span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs bg-red-900/30 hover:bg-red-900/60 text-red-300 hover:text-red-100 border border-red-800/50 px-3.5 py-1.5 rounded-full font-semibold transition cursor-pointer">
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="max-w-4xl mx-auto px-6 pt-8 space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <a href="{{ route('books.show', $book->id) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-wine-700 hover:text-wine-900 transition bg-white border border-wine-200 px-4 py-2.5 rounded-2xl shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Detail Buku
            </a>

            <div class="bg-gold-50 border border-gold-300 text-wine-800 px-4 py-2 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-gold-500 animate-pulse"></span>
                RuangPustaka - Copyright Protected 2026
            </div>
        </div>

        <!-- Kartu E-Reader -->
        <div class="bg-white border border-wine-100 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-wine-100 pb-4 gap-4">
                <div>
                    <h1 class="text-lg md:text-xl font-bold text-ink-900 font-serif">{{ $book->title }}</h1>
                    <p class="text-xs text-wine-500">Penulis: {{ $book->author ?? 'Tidak Diketahui' }}</p>
                </div>

                <!-- Tombol Bookmark -->
                <button id="bookmark-btn" onclick="toggleBookmark()" class="flex items-center gap-2 bg-gold-50 hover:bg-gold-100 border border-gold-300 text-wine-800 text-xs font-bold px-4 py-2 rounded-2xl transition cursor-pointer shadow-xs">
                    <svg id="bookmark-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    <span id="bookmark-text">Tandai Halaman Ini</span>
                </button>
            </div>

            <!-- Area Tampilan Dokumen -->
            <div id="canvas-container" class="w-full bg-white border border-wine-100 rounded-2xl overflow-hidden flex flex-col items-center justify-center relative shadow-sm min-h-[500px]">
                <div class="watermark">{{ Auth::user()->name ?? 'Ruang Pustaka' }} - Protected Document</div>
                <!--
                  UBAH TEKS DI SINI: Pesan ketika user mencoba melakukan klik kanan/salin
                -->
                <div class="secure-overlay" onclick="showToast('Konten ini dilindungi hak cipta.', 'warning');"></div>
                <canvas id="pdf-canvas" class="w-full h-auto block object-contain"></canvas>
                <div id="loading-text" class="text-xs font-semibold text-wine-400 py-10">Memuat berkas PDF...</div>
            </div>

            <!-- Navigasi Bawah: Tombol Pop-up Pilih Halaman & Tombol Slide -->
            <div class="flex flex-col sm:flex-row justify-between items-center pt-2 gap-4 relative">

                <!-- Tombol Trigger Pop-up Pilih Halaman -->
                <div class="relative">
                    <button onclick="togglePageModal()" class="flex items-center gap-2 bg-wine-50 hover:bg-wine-100 border border-wine-200 text-wine-800 text-xs font-bold px-4 py-2.5 rounded-2xl transition cursor-pointer shadow-xs">
                        <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                        <span>Pilih Halaman (<span id="current-page-label">1</span>)</span>
                    </button>

                    <!-- Pop-up Modal Daftar Halaman -->
                    <div id="page-modal" class="hidden absolute left-0 bottom-12 w-64 bg-white border border-wine-200 rounded-2xl shadow-xl z-30 p-3 flex flex-col">
                        <div class="flex justify-between items-center pb-2 mb-2 border-b border-wine-100 px-1">
                            <span class="text-xs font-bold text-ink-900">Daftar Halaman</span>
                        </div>
                        <div id="page-list-container" class="max-h-56 overflow-y-auto space-y-1 pr-1">
                            <!-- Daftar halaman di-generate otomatis via JS -->
                        </div>
                    </div>
                </div>

                <!-- Tombol Navigasi Sebelumnya & Selanjutnya -->
                <div class="flex items-center gap-3 bg-wine-50 border border-wine-200 px-4 py-2.5 rounded-2xl shadow-xs">
                    <button id="prev-page" class="bg-white border border-wine-200 hover:bg-wine-100 text-wine-800 text-xs font-bold px-3.5 py-2 rounded-xl transition cursor-pointer shadow-xs">
                        ‹ Sebelumnya
                    </button>
                    <span class="text-xs font-bold text-wine-800 whitespace-nowrap px-2">
                        Hal <span id="page-num">1</span> dari <span id="page-count">-</span>
                    </span>
                    <button id="next-page" class="bg-white border border-wine-200 hover:bg-wine-100 text-wine-800 text-xs font-bold px-3.5 py-2 rounded-xl transition cursor-pointer shadow-xs">
                        Selanjutnya ›
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Custom Toast Notification Container (Tempat munculnya pop-up notifikasi baru) -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <!-- Skrip Slider PDF.js dengan Modal Pop-up & Bookmark Badge -->
    <script>
        @php
            $rawPdf = $book->file_pdf ?? $book->file_path ?? '';
            $pdfRelUrl = !empty($rawPdf) ? '/storage/' . ltrim($rawPdf, '/') : '';
        @endphp

        const pdfUrl = "{{ $pdfRelUrl }}";
        const bookId = "{{ $book->id }}";

        let pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            canvas = document.getElementById('pdf-canvas'),
            ctx = canvas.getContext('2d'),
            loadingText = document.getElementById('loading-text');

        const bookmarkKey = 'ruang_pustaka_bookmark_' + bookId;
        let savedBookmark = localStorage.getItem(bookmarkKey);
        if (savedBookmark) {
            pageNum = parseInt(savedBookmark);
        }

        // Fungsi Custom Toast Notification (Pengganti alert bawaan browser)
        function showToast(message, type = 'info') {
            let container = document.getElementById('toast-container');
            let toast = document.createElement('div');

            let bgClass = 'bg-wine-900 text-white';
            let iconSvg = '<svg class="w-4 h-4 text-gold-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

            if (type === 'success') {
                bgClass = 'bg-emerald-600 text-white';
                iconSvg = '<svg class="w-4 h-4 text-emerald-100 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            } else if (type === 'warning') {
                bgClass = 'bg-gold-500 text-ink-900';
                iconSvg = '<svg class="w-4 h-4 text-ink-900 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
            }

            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl text-xs font-semibold transition-all transform translate-y-2 opacity-0 duration-300 ${bgClass}`;
            toast.innerHTML = `${iconSvg} <span>${message}</span>`;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        function renderPage(num) {
            pageRendering = true;
            pdfDoc.getPage(num).then(function(page) {
                let containerWidth = canvas.parentElement.clientWidth || 800;
                let viewport = page.getViewport({ scale: 1.0 });
                let scale = containerWidth / viewport.width;
                let scaledViewport = page.getViewport({ scale: scale });

                canvas.height = scaledViewport.height;
                canvas.width = scaledViewport.width;

                let renderContext = {
                    canvasContext: ctx,
                    viewport: scaledViewport
                };
                let renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });

            document.getElementById('page-num').textContent = num;
            document.getElementById('current-page-label').textContent = num;
            updateBookmarkButtonState(num);
            highlightActivePageInModal(num);
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function onPrevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }

        function jumpToPage(targetPage) {
            let p = parseInt(targetPage);
            if (p >= 1 && p <= pdfDoc.numPages) {
                pageNum = p;
                queueRenderPage(pageNum);
                togglePageModal();
            }
        }

        // Kontrol Tampilan Modal Pop-up
        function togglePageModal() {
            let modal = document.getElementById('page-modal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                buildPageList();
                let activeItem = document.getElementById('modal-page-item-' + pageNum);
                if (activeItem) {
                    activeItem.scrollIntoView({ block: 'nearest' });
                }
            }
        }

        window.addEventListener('click', function(e) {
            let modal = document.getElementById('page-modal');
            let triggerBtn = modal.previousElementSibling;
            if (!modal.contains(e.target) && !triggerBtn.contains(e.target) && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });

        function buildPageList() {
            let container = document.getElementById('page-list-container');
            container.innerHTML = '';
            let currentBookmark = localStorage.getItem(bookmarkKey);

            for (let i = 1; i <= pdfDoc.numPages; i++) {
                let isBookmarked = (currentBookmark == i);
                let btn = document.createElement('button');
                btn.type = 'button';
                btn.id = 'modal-page-item-' + i;
                btn.className = `w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition cursor-pointer ${i === pageNum ? 'bg-wine-700 text-white' : 'hover:bg-wine-50 text-wine-800'}`;

                let labelSpan = document.createElement('span');
                labelSpan.textContent = 'Halaman ' + i;
                btn.appendChild(labelSpan);

                if (isBookmarked) {
                    let badge = document.createElement('span');
                    badge.className = `text-[10px] px-2 py-0.5 rounded-md font-bold ${i === pageNum ? 'bg-gold-400 text-ink-900' : 'bg-gold-100 text-gold-600'}`;
                    badge.textContent = 'Bookmark';
                    btn.appendChild(badge);
                }

                btn.onclick = function() {
                    jumpToPage(i);
                };
                container.appendChild(btn);
            }
        }

        function highlightActivePageInModal(num) {
            let currentBookmark = localStorage.getItem(bookmarkKey);
            let container = document.getElementById('page-list-container');
            if (container.children.length > 0) {
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    let item = document.getElementById('modal-page-item-' + i);
                    if (item) {
                        if (i === num) {
                            item.className = "w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition cursor-pointer bg-wine-700 text-white";
                            let badge = item.querySelector('span:nth-child(2)');
                            if(badge) badge.className = "text-[10px] px-2 py-0.5 rounded-md font-bold bg-gold-400 text-ink-900";
                        } else {
                            item.className = "w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition cursor-pointer hover:bg-wine-50 text-wine-800";
                            let badge = item.querySelector('span:nth-child(2)');
                            if(badge) badge.className = "text-[10px] px-2 py-0.5 rounded-md font-bold bg-gold-100 text-gold-600";
                        }
                    }
                }
            }
        }

        // Fungsi Bookmark (Menggunakan Custom Toast Notification)
        function toggleBookmark() {
            let currentBookmark = localStorage.getItem(bookmarkKey);
            if (currentBookmark == pageNum) {
                localStorage.removeItem(bookmarkKey);
                // UBAH TEKS DI SINI: Pesan saat bookmark dihapus
                showToast('Bookmark dihapus dari halaman ' + pageNum, 'warning');
            } else {
                localStorage.setItem(bookmarkKey, pageNum);
                // UBAH TEKS DI SINI: Pesan saat bookmark berhasil ditambahkan
                showToast('Berhasil menandai halaman ' + pageNum, 'success');
            }
            updateBookmarkButtonState(pageNum);
            buildPageList();
        }

        function updateBookmarkButtonState(num) {
            let currentBookmark = localStorage.getItem(bookmarkKey);
            let btnText = document.getElementById('bookmark-text');
            let icon = document.getElementById('bookmark-icon');

            if (currentBookmark == num) {
                btnText.textContent = 'Halaman Ditandai (Bookmarked)';
                icon.setAttribute('fill', 'currentColor');
            } else {
                btnText.textContent = 'Tandai Halaman Ini';
                icon.setAttribute('fill', 'none');
            }
        }

        if (pdfUrl) {
            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdfDoc_) {
                pdfDoc = pdfDoc_;
                if (loadingText) loadingText.style.display = 'none';
                document.getElementById('page-count').textContent = pdfDoc.numPages;

                buildPageList();
                renderPage(pageNum);
            }).catch(function(error) {
                console.error('Gagal memuat PDF:', error);
                if (loadingText) {
                    loadingText.textContent = 'Gagal memuat berkas PDF. Pastikan file tersedia di folder storage.';
                    loadingText.className = 'text-xs font-semibold text-red-500 py-10';
                }
            });
        } else {
            if (loadingText) {
                loadingText.textContent = 'File PDF tidak ditemukan untuk buku ini.';
                loadingText.className = 'text-xs font-semibold text-gold-600 py-10';
            }
        }

        document.getElementById('prev-page').addEventListener('click', onPrevPage);
        document.getElementById('next-page').addEventListener('click', onNextPage);

        // Shortcut Keyboard & Navigasi Panah
        document.addEventListener('keydown', function (e) {
            if (
                e.keyCode == 123 ||
                (e.ctrlKey && e.shiftKey && (e.keyCode == 73 || e.keyCode == 74)) ||
                (e.ctrlKey && e.keyCode == 85) ||
                (e.ctrlKey && e.keyCode == 83) ||
                (e.ctrlKey && e.keyCode == 80) ||
                (e.ctrlKey && e.keyCode == 67) ||
                e.keyCode == 44
            ) {
                e.preventDefault();
                // UBAH TEKS DI SINI: Pesan ketika shortcut terdeteksi
                showToast('Aksi ini dinonaktifkan demi keamanan hak cipta.', 'warning');
                return false;
            }

            if (e.key === 'ArrowLeft') {
                onPrevPage();
            } else if (e.key === 'ArrowRight') {
                onNextPage();
            }
        });
    </script>
</body>

</html>