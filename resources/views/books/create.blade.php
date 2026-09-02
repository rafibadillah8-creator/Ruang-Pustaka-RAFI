<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah E-Book Baru - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen py-10">

    <div class="max-w-3xl mx-auto px-6">
        <!-- Perbaikan Route Kembali ke Dashboard Admin -->
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-[#2563EB] hover:underline text-sm mb-6 transition font-medium">
            ← Kembali ke Dashboard Admin
        </a>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-[#0F172A] mb-2">Tambah E-Book Baru</h1>
            <p class="text-xs text-[#64748B] mb-6">Isi formulir di bawah ini untuk menambahkan koleksi buku baru ke katalog.</p>

            <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Pilihan Kategori Buku -->
                 
<div class="space-y-2">
    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kategori Buku</label>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
        @forelse($categories as $cat)
            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                <input type="checkbox" name="category[]" value="{{ $cat->name }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>{{ $cat->name }}</span>
            </label>
        @empty
            <span class="text-xs text-slate-400">Belum ada kategori tersedia.</span>
        @endforelse
    </div>
</div>

                <!-- Judul Buku -->
                <div>
                    <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Judul Buku</label>
                    <input type="text" name="title" required placeholder="Masukkan judul buku" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#2563EB]">
                </div>

                <!-- Penulis & Penerbit -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Penulis</label>
                        <input type="text" name="author" required placeholder="Nama penulis" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#2563EB]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Penerbit</label>
                        <input type="text" name="publisher" required placeholder="Nama penerbit" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#2563EB]">
                    </div>
                </div>

                <!-- Harga Buku -->
                <div>
                    <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Harga (Rp)</label>
                    <input type="number" name="price" value="0" min="0" required placeholder="Isi 0 jika gratis" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#2563EB]">
                    <span class="text-[11px] text-[#64748B] mt-1 block">Isi dengan angka 0 jika buku bersifat gratis.</span>
                </div>

                <!-- Upload File Gambar & PDF -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Gambar Sampul / Cover</label>
                        <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#2563EB] hover:file:bg-blue-100">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">File E-Book (PDF)</label>
                        <input type="file" name="file_path" accept=".pdf" class="w-full text-xs text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#2563EB] hover:file:bg-blue-100">
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-[#64748B] hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-[#2563EB] hover:bg-blue-700 text-white text-xs px-6 py-2.5 rounded-xl font-semibold transition shadow-sm">
                        Simpan Buku Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>