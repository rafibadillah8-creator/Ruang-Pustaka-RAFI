<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Voucher - Ruang Pustaka</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen pb-16 antialiased">

    <nav class="border-b border-slate-200 bg-white px-6 py-4 flex justify-between items-center sticky top-0 z-50">
        <a href="{{ url('/') }}" class="text-xl font-bold text-blue-600">Ruang Pustaka</a>
        <a href="{{ route('admin.vouchers.index') }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition bg-white border border-slate-200 px-4 py-2.5 rounded-2xl shadow-sm">
            ← Kembali
        </a>
    </nav>

    <main class="max-w-3xl mx-auto px-6 pt-8 pb-16 space-y-6">

        <h1 class="text-xl font-bold text-slate-900">Edit Voucher</h1>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST" class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="text-xs font-bold text-slate-800 uppercase tracking-wide">Kode Voucher</label>
                <input type="text" name="code" value="{{ old('code', $voucher->code) }}" placeholder="Contoh: HEMAT10"
                    class="mt-2 w-full border border-slate-200 rounded-xl px-4 py-3 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wide">Jenis Diskon</label>
                    <select name="type" id="type" onchange="toggleValueLabel()"
                        class="mt-2 w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="percentage" {{ old('type', $voucher->type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wide" id="value-label">Nilai Diskon</label>
                    <input type="number" step="0.01" min="0" name="value" value="{{ old('value', $voucher->value) }}"
                        class="mt-2 w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-800 uppercase tracking-wide">Berlaku Untuk</label>
                <select name="scope" id="scope" onchange="toggleBookList()"
                    class="mt-2 w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="all" {{ old('scope', $voucher->scope) === 'all' ? 'selected' : '' }}>Semua Buku</option>
                    <option value="specific" {{ old('scope', $voucher->scope) === 'specific' ? 'selected' : '' }}>Buku Tertentu</option>
                </select>
            </div>

            @php
                $selectedBooks = old('books', $voucher->books->pluck('id')->toArray());
            @endphp

            <div id="book-list-wrapper" class="{{ $voucher->scope === 'specific' ? '' : 'hidden' }}">
                <label class="text-xs font-bold text-slate-800 uppercase tracking-wide">Pilih Buku</label>
                <div class="mt-2 border border-slate-200 rounded-xl max-h-56 overflow-y-auto p-3 space-y-1">
                    @forelse($books as $book)
                        <label class="flex items-center gap-2 py-1.5 text-sm text-slate-700 cursor-pointer">
                            <input type="checkbox" name="books[]" value="{{ $book->id }}"
                                {{ collect($selectedBooks)->contains($book->id) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            {{ $book->title }}
                        </label>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada buku tersedia.</p>
                    @endforelse
                </div>
                <p class="text-xs text-slate-400 mt-1">Kamu bisa mencentang lebih dari satu buku.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wide">Batas Pemakaian</label>
                    <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $voucher->usage_limit) }}" placeholder="Kosongkan jika tanpa batas"
                        class="mt-2 w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-slate-400 mt-1">Sudah dipakai {{ $voucher->used_count }} kali.</p>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-800 uppercase tracking-wide">Tanggal Kadaluarsa</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at', $voucher->expires_at?->format('Y-m-d')) }}"
                        class="mt-2 w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    Voucher aktif
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.vouchers.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-5 py-3 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-6 py-3 rounded-xl shadow-sm transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </main>

    <script>
        function toggleValueLabel() {
            const type = document.getElementById('type').value;
            document.getElementById('value-label').textContent = type === 'percentage' ? 'Nilai Diskon (%)' : 'Nilai Diskon (Rp)';
        }
        function toggleBookList() {
            const scope = document.getElementById('scope').value;
            const wrapper = document.getElementById('book-list-wrapper');
            wrapper.classList.toggle('hidden', scope !== 'specific');
        }
        toggleValueLabel();
        toggleBookList();
    </script>
</body>

</html>