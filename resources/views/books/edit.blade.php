<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Buku - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen flex antialiased">

    <div class="w-full min-h-screen bg-[#F8FAFC] flex flex-col relative pb-24">
        
        <header class="px-8 py-5 flex items-center justify-between border-b border-slate-100 bg-white/80 backdrop-blur-md sticky top-0 z-50 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md">RP</div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Admin Panel</h1>
                    <p class="text-[11px] text-slate-400">Halo, {{ Auth::user()->name }}</p>
                </div>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-100 text-slate-600 px-3.5 py-1.5 rounded-full font-semibold hover:bg-slate-200 transition">← Kembali</a>
        </header>

        <main class="flex-1 max-w-4xl w-full mx-auto px-6 py-6 space-y-6">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-6 text-white shadow-lg">
                <span class="bg-white/20 text-[10px] px-3 py-1 rounded-full font-medium uppercase">Formulir Admin</span>
                <h2 class="text-xl font-bold mt-3">Edit Data Buku: {{ $book->title }}</h2>
                <p class="text-xs text-blue-100 mt-1">Perbarui informasi detail mengenai koleksi buku pustaka.</p>
            </div>

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

                <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Kategori Buku -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kategori Buku (Pilih Maksimal 4)</label>
                        
                        <div class="border border-slate-200 rounded-2xl p-4 max-h-48 overflow-y-auto space-y-2.5 bg-slate-50/50">
                            @php
                                // Mengambil ID kategori yang sudah dipilih sebelumnya oleh buku ini
                                $selectedCategories = $book->categories->pluck('id')->toArray();
                            @endphp

                            @forelse($categories as $cat)
                                <label class="flex items-center gap-3 p-2 hover:bg-white rounded-xl cursor-pointer transition border border-transparent hover:border-slate-100">
                                    <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                                        class="category-checkbox w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500"
                                        {{ in_array($cat->id, $selectedCategories) ? 'checked' : '' }}>
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
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Judul Buku</label>
                        <input type="text" name="title" value="{{ old('title', $book->title) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-blue-600">
                    </div>

                    <!-- Grid Penulis & Penerbit -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Penulis</label>
                            <input type="text" name="author" value="{{ old('author', $book->author) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-blue-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Penerbit</label>
                            <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Harga (Rp) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Harga (Rp)</label>
                        <input type="number" name="price" value="{{ old('price', $book->price) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-blue-600">
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Deskripsi</label>
                        <textarea name="description" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-blue-600">{{ old('description', $book->description) }}</textarea>
                    </div>

                    <!-- Tombol Aksi Form -->
                    <div class="pt-4 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600">Batal</a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-blue-600 text-white shadow-md hover:bg-blue-700">Simpan Perubahan</button>
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
    </div>
</body>
</html>