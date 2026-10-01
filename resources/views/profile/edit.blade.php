<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Ruang Pustaka</title>

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
        <main class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
            <h1 class="text-2xl sm:text-3xl font-bold font-serif text-wine-900 mb-1">Profil Saya</h1>
            <p class="text-sm text-stone-500 mb-6">Kelola informasi akun kamu di sini.</p>

            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3 mb-5">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-stone-300 focus:border-gold-500 focus:ring-1 focus:ring-gold-400 rounded-xl px-4 py-2.5 text-sm outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full border border-stone-300 focus:border-gold-500 focus:ring-1 focus:ring-gold-400 rounded-xl px-4 py-2.5 text-sm outline-none transition">
                </div>

                <hr class="border-stone-200">

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti"
                        class="w-full border border-stone-300 focus:border-gold-500 focus:ring-1 focus:ring-gold-400 rounded-xl px-4 py-2.5 text-sm outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                        class="w-full border border-stone-300 focus:border-gold-500 focus:ring-1 focus:ring-gold-400 rounded-xl px-4 py-2.5 text-sm outline-none transition">
                </div>

                <button type="submit" class="w-full bg-gold-500 hover:bg-gold-400 text-ink-900 font-bold py-2.5 rounded-xl transition shadow-sm hover:shadow-md">
                    Simpan Perubahan
                </button>
            </form>
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