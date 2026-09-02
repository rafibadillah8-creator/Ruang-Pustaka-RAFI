<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Buku - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen flex antialiased">

    <!-- Container Utama Full Layar (Konsisten dengan Dashboard Admin) -->
    <div class="w-full min-h-screen bg-[#F8FAFC] flex flex-col relative pb-24">
        
        <!-- Header Atas (Profil & Selamat Datang) -->
        <header class="px-8 py-5 flex items-center justify-between border-b border-slate-100 bg-white/80 backdrop-blur-md sticky top-0 z-50 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-blue-200">
                    RP
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Admin Panel</h1>
                    <p class="text-[11px] text-slate-400">Halo, {{ Auth::user()->name }}</p>
                </div>
            </div>

            <!-- Tombol Kembali / Logout -->
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-100 text-slate-600 px-3.5 py-1.5 rounded-full font-semibold hover:bg-slate-200 transition">
                    ← Kembali
                </a>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 max-w-4xl w-full mx-auto px-6 py-6 space-y-6">
            
            <!-- Banner Card Edit -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-200">
                <span class="bg-white/20 text-[10px] px-3 py-1 rounded-full font-medium uppercase tracking-wider">Formulir Admin</span>
                <h2 class="text-xl font-bold mt-3">Edit Data Buku</h2>
                <p class="text-xs text-blue-100 mt-1">Perbarui informasi detail mengenai koleksi buku pustaka.</p>
            </div>

            <!-- Card Form Utama -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
                
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs space-y-1">
                        <p class="font-bold">Terjadi kesalahan input:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Kategori Buku (Checkbox dengan Scrollbar, Max 4 Pilihan) -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kategori Buku (Pilih Maksimal 4)</label>
                        
                        <!-- Kotak Scroll Bar untuk Kategori -->
                        <div class="border border-slate-200 rounded-2xl p-4 max-h-48 overflow-y-auto space-y-2.5 bg-slate-50/50">
                            @php
                                // Memecah string kategori buku saat ini menjadi array untuk proses centang
                                $selectedCategories = isset($book->category) ? array_map('trim', explode(',', $book->category)) : [];
                            @endphp

                            @forelse($categories as $cat)
                                <label class="flex items-center gap-3 p-2 hover:bg-white rounded-xl cursor-pointer transition border border-transparent hover:border-slate-100">
                                    <input type="checkbox" name="category[]" value="{{ $cat->name }}" 
                                        class="category-checkbox w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500"
                                        {{ in_array($cat->name, $selectedCategories) ? 'checked' : '' }}>
                                    <span class="text-xs font-semibold text-slate-700">{{ $cat->name }}</span>
                                </label>
                            @empty
                                <p class="text-xs text-slate-400 text-center py-2">Belum ada kategori tersedia.</p>
                            @endforelse
                        </div>
                        <p class="text-[10px] text-slate-400">Kamu bisa mencentang lebih dari satu kategori (maksimal 4).</p>
                    </div>

                    <!-- Judul Buku -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Buku</label>
                        <input 
                            type="text" 
                            name="title" 
                            value="{{ old('title', $book->title) }}" 
                            required
                            placeholder="Masukkan judul buku..." 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-[#0F172A] focus:outline-none focus:border-blue-600 focus:bg-white transition"
                        >
                    </div>

                    <!-- Grid Penulis & Penerbit -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penulis</label>
                            <input 
                                type="text" 
                                name="author" 
                                value="{{ old('author', $book->author) }}" 
                                required
                                placeholder="Nama penulis..." 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-[#0F172A] focus:outline-none focus:border-blue-600 focus:bg-white transition"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penerbit</label>
                            <input 
                                type="text" 
                                name="publisher" 
                                value="{{ old('publisher', $book->publisher ?? '') }}" 
                                placeholder="Nama penerbit..." 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-[#0F172A] focus:outline-none focus:border-blue-600 focus:bg-white transition"
                            >
                        </div>
                    </div>

                    <!-- Harga (Rp) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Harga (Rp)</label>
                        <input 
                            type="number" 
                            name="price" 
                            value="{{ old('price', $book->price) }}" 
                            required
                            placeholder="0 untuk gratis..." 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-[#0F172A] focus:outline-none focus:border-blue-600 focus:bg-white transition"
                        >
                    </div>

                    <!-- Cover Buku -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cover Buku (Opsional)</label>
                        @if($book->cover_image)
                            <div class="mb-3 flex items-center gap-3">
                                <div class="w-12 h-16 bg-slate-100 rounded-xl overflow-hidden border border-slate-200 flex-shrink-0">
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" class="w-full h-full object-cover">
                                </div>
                                <span class="text-[11px] text-slate-400">Cover saat ini terpasang. Unggah file baru di bawah jika ingin menggantinya.</span>
                            </div>
                        @endif
                        <input 
                            type="file" 
                            name="cover_image" 
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                        >
                    </div>

                    <!-- Tombol Aksi Form -->
                    <div class="pt-4 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-blue-600 text-white shadow-md shadow-blue-200 hover:bg-blue-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>

        </main>

        <!-- Script JavaScript untuk Membatasi Maksimal 4 Pilihan Kategori -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const checkboxes = document.querySelectorAll('.category-checkbox');
                
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
                        if (checkedCount > 4) {
                            this.checked = false;
                            alert('Maksimal kategori yang dapat dipilih adalah 4!');
                        }
                    });
                });
            });
        </script>

        <!-- Navigasi Bawah Konsisten -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-slate-100 px-6 py-3 flex justify-center gap-12 items-center z-50 shadow-lg">
            <a href="{{ route('home') }}" class="flex flex-col items-center px-4 {{ request()->routeIs('home') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="text-[10px] font-medium mt-1">Beranda</span>
            </a>

            <a href="{{ route('books.index') }}" class="flex flex-col items-center px-4 {{ request()->routeIs('books.index') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="text-[10px] font-medium mt-1">Koleksi</span>
            </a>

            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center px-4 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="text-[10px] font-medium mt-1">Admin</span>
            </a>
        </nav>

    </div>

</body>
</html>