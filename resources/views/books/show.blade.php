<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book->title }} - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen py-10">

    <div class="max-w-4xl mx-auto px-6">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('books.index') }}"
                class="inline-flex items-center text-[#2563EB] hover:underline text-sm transition font-medium">
                ← Kembali ke Katalog Buku
            </a>

            <!-- Tombol Admin (Edit & Hapus) di Halaman Detail -->
            @if(Auth::check() && (strtolower(Auth::user()->role ?? '') === 'admin' || Auth::user()->is_admin))
                <div class="flex items-center gap-2">
                    <a href="{{ route('books.edit', $book->id) }}"
                        class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit Buku
                    </a>

                    <form action="{{ route('books.destroy', $book->id) }}" method="POST"
                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus buku ini?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm flex flex-col md:flex-row gap-8">
            <!-- Cover Buku -->
            <div class="w-full md:w-1/3 flex-shrink-0">
                <div
                    class="aspect-[3/4] bg-slate-100 rounded-xl overflow-hidden border border-slate-200 shadow-md flex items-center justify-center">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                            class="w-full h-full object-cover">
                    @else
                        <span class="text-[#64748B] text-sm">Tidak Ada Cover</span>
                    @endif
                </div>
            </div>

            <!-- Detail Informasi Buku -->
            <div class="w-full md:w-2/3 flex flex-col justify-between">
                <div>
                    <span
                        class="inline-block bg-blue-50 text-[#2563EB] text-xs px-3 py-1 rounded-full font-semibold mb-3 border border-blue-100">
                        {{ $book->category ?? 'Tanpa Kategori' }}
                    </span>
                    <h1 class="text-3xl font-bold text-[#0F172A] mb-2">{{ $book->title }}</h1>
                    <p class="text-[#64748B] text-sm mb-6">Oleh <strong
                            class="text-[#0F172A]">{{ $book->author }}</strong> | Penerbit: <strong
                            class="text-[#0F172A]">{{ $book->publisher }}</strong></p>

                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 mb-6">
                        <span class="text-xs text-[#64748B] block mb-1">Harga</span>
                        <span class="text-2xl font-bold text-emerald-600">
                            {{ $book->price > 0 ? 'Rp ' . number_format($book->price, 0, ',', '.') : 'Gratis' }}
                        </span>
                    </div>
                </div>

                <!-- Action Button untuk File PDF -->
                <div class="pt-4 border-t border-slate-100">
                    @if($book->file_path)
                        <a href="{{ asset('storage/' . $book->file_path) }}" target="_blank"
                            class="inline-flex items-center justify-center w-full bg-[#2563EB] hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition shadow-sm text-sm">
                            📖 Baca / Download PDF
                        </a>
                    @else
                        <button disabled
                            class="w-full bg-slate-100 text-[#64748B] font-semibold px-6 py-3 rounded-xl text-sm cursor-not-allowed">
                            File PDF Belum Tersedia
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>

</html>