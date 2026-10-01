<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Saya - Ruang Pustaka</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-parchment text-ink-900 font-sans antialiased min-h-screen flex flex-col justify-between">

    <div>
        <!-- Navbar -->
        <nav class="border-b border-wine-900 bg-gradient-to-r from-wine-900 via-ink-900 to-wine-900 px-4 sm:px-8 py-3.5 flex justify-between items-center sticky top-0 z-50">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-lg sm:text-xl font-bold text-gold-400 hover:text-gold-500 transition">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka" class="h-10 w-10 object-contain">
                <span class="font-serif">Ruang Pustaka</span>
            </a>
            <a href="{{ route('home') }}" class="text-xs sm:text-sm font-semibold text-wine-100 hover:text-gold-400 px-3 py-2 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </nav>

        <!-- Main Content -->
        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
            <h1 class="text-2xl sm:text-3xl font-bold font-serif text-wine-900 mb-1">Daftar Buku Saya</h1>
            <p class="text-sm text-stone-500 mb-6">Koleksi e-book yang sudah kamu beli.</p>

            @if ($books->isEmpty())
                <div class="bg-white border border-stone-200 rounded-2xl p-10 text-center">
                    <svg class="w-14 h-14 mx-auto mb-3 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-stone-500 text-sm mb-4">Kamu belum punya buku. Yuk mulai jelajahi koleksi kami!</p>
                    <a href="{{ route('books.index') }}" class="inline-block bg-gold-500 hover:bg-gold-400 text-ink-900 font-bold px-5 py-2.5 rounded-xl transition shadow-sm hover:shadow-md text-sm">
                        Jelajahi Buku
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach ($books as $book)
                        <div class="bg-wine-100 border border-wine-300 rounded-2xl p-3 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between group/card">
                            <a href="{{ route('books.read', $book->id) }}" class="block w-full h-52 bg-stone-100 rounded-xl overflow-hidden mb-3 relative border border-ink-900/15">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover/card:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-stone-100 text-stone-300 p-2 text-center">
                                        <svg class="w-12 h-12 mb-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2a2 2 0 0 1 2 2v1h3a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3V4a2 2 0 0 1 2-2zm-3 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-6 6h6v1H9v-1z"/>
                                        </svg>
                                        <span class="text-[10px] text-stone-400">Tidak ada sampul</span>
                                    </div>
                                @endif
                            </a>
                            <a href="{{ route('books.read', $book->id) }}" class="font-bold text-sm text-stone-900 truncate mb-1 block hover:text-wine-700 transition" title="{{ $book->title }}">
                                {{ $book->title }}
                            </a>
                            <p class="text-xs text-stone-500 truncate mb-2">{{ $book->author }}</p>
                            <a href="{{ route('books.read', $book->id) }}" class="w-full text-center text-xs sm:text-sm font-semibold bg-wine-700 hover:bg-wine-800 text-white py-2 rounded-xl transition">
                                Baca Sekarang
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>

    <!-- Footer -->
    <footer class="border-t border-wine-900 bg-gradient-to-r from-wine-900 via-ink-900 to-wine-900 mt-16 py-6 text-center text-xs text-wine-200">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-2 font-semibold text-wine-50">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Ruang Pustaka" class="h-7 w-7 object-contain">
                Ruang Pustaka
            </div>
            <p>&copy; {{ date('Y') }} Ruang Pustaka. Semua hak cipta dilindungi.</p>
        </div>
    </footer>
</body>
</html>