<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Voucher - Ruang Pustaka</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>

<body class="bg-[#fdfbf7] text-[#3a261f] min-h-screen flex antialiased" x-data="{ openModal: false }">

    <!-- Container Utama Full Layar -->
    <div class="w-full min-h-screen bg-white shadow-xl flex flex-col relative pb-24">

        <!-- Header Atas -->
        <header class="px-8 py-5 flex items-center justify-between border-b border-stone-200 bg-[#fdfbf7]/85 backdrop-blur-md sticky top-0 z-40">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#c19b6c] flex items-center justify-center text-white font-bold text-lg shadow-md shadow-[#c19b6c]/50">
                    RP
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Admin Panel</h1>
                    <p class="text-[11px] text-slate-400">Manajemen Voucher Diskon</p>
                </div>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-50 text-slate-600 border border-slate-200 px-3.5 py-1.5 rounded-full font-semibold hover:bg-slate-100 transition">
                ← Kembali
            </a>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-8 py-6 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Banner Manajemen Voucher -->
            <div class="bg-gradient-to-r from-[#3a261f] to-[#2c1d18] rounded-3xl p-8 text-white shadow-lg shadow-[#3a261f]/20 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-[10px] uppercase tracking-wider bg-[#c19b6c]/40 px-3 py-1 rounded-full font-bold">Diskon & Promo</span>
                    <h2 class="text-2xl font-bold mt-3 text-white">Manajemen Voucher</h2>
                    <p class="text-[#e8dcc4] text-xs mt-1">Buat, pantau, dan kelola kode voucher perpustakaan.</p>
                </div>
                <!-- Tombol Trigger Popup Modal -->
                <button @click="openModal = true" class="bg-[#fdfbf7] text-[#3a261f] px-4 py-2.5 rounded-2xl font-semibold text-xs shadow hover:bg-white transition inline-flex items-center gap-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Voucher Baru
                </button>
            </div>

            <!-- Tabel Daftar Voucher -->
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-4">Daftar Voucher Aktif</h3>
                
                @if(isset($vouchers) && count($vouchers) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-stone-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 px-4">Kode</th>
                                    <th class="py-3 px-4">Diskon</th>
                                    <th class="py-3 px-4">Berlaku Untuk</th>
                                    <th class="py-3 px-4">Terpakai</th>
                                    <th class="py-3 px-4">Kadaluarsa</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 text-xs text-slate-700">
                                @foreach($vouchers as $voucher)
                                    <tr class="hover:bg-[#fdfbf7]/50 transition">
                                        <td class="py-3.5 px-4 font-bold text-slate-900 font-mono">{{ $voucher->code }}</td>
                                        <td class="py-3.5 px-4 text-slate-600 font-semibold">
                                            @if($voucher->type === 'percentage')
                                                {{ rtrim(rtrim(number_format($voucher->value, 2), '0'), '.') }}%
                                            @else
                                                Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500">
                                            {{ $voucher->scope === 'all' ? 'Semua Buku' : $voucher->books->count() . ' Buku Tertentu' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600">
                                            {{ $voucher->used_count }}{{ $voucher->usage_limit ? ' / ' . $voucher->usage_limit : '' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-400">
                                            {{ $voucher->expires_at ? $voucher->expires_at->format('d M Y') : 'Tidak ada' }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @if($voucher->is_active)
                                                <span class="inline-block bg-[#fdfbf7] text-[#8b5e3c] px-2.5 py-1 rounded-md font-semibold border border-[#c19b6c]/30">Aktif</span>
                                            @else
                                                <span class="inline-block bg-slate-100 text-slate-500 px-2.5 py-1 rounded-md font-semibold">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right space-x-2">
                                            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="text-[#8b5e3c] font-semibold hover:underline">Edit</a>
                                            <span class="text-slate-300">|</span>
                                            <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus voucher ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 font-semibold hover:underline cursor-pointer">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($vouchers, 'links'))
                        <div class="pt-4">
                            {{ $vouchers->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 text-slate-400 text-xs">
                        Belum ada voucher yang dibuat.
                    </div>
                @endif
            </div>

        </main>

        <!-- POPUP MODAL BUAT VOUCHER -->
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 overflow-y-auto" style="display: none;" x-transition.opacity>
            <div @click.away="openModal = false" class="bg-white rounded-3xl shadow-2xl max-w-xl w-full p-8 relative space-y-6 my-8 border border-slate-100">
                
                <!-- Tombol Close Modal -->
                <button @click="openModal = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition">
                    Tutup
                </button>

                <div>
                    <h3 class="text-xl font-bold text-slate-900">Buat Voucher Baru</h3>
                    <p class="text-xs text-slate-400 mt-1">Isi formulir di bawah untuk menambahkan diskon baru.</p>
                </div>

                <!-- Form Store Voucher -->
                <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kode Voucher</label>
                        <input type="text" name="code" placeholder="CONTOH: HEMAT10" required class="w-full bg-[#fdfbf7] border border-stone-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-[#8b5e3c] transition">
                        <p class="text-[10px] text-slate-400 mt-1">Kode akan otomatis disimpan dalam huruf kapital.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Diskon</label>
                            <select name="type" class="w-full bg-[#fdfbf7] border border-stone-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-[#8b5e3c] transition">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal Tetap (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nilai Diskon</label>
                            <input type="number" name="value" placeholder="Contoh: 10" step="any" required class="w-full bg-[#fdfbf7] border border-stone-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-[#8b5e3c] transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Berlaku Untuk</label>
                        <select name="scope" class="w-full bg-[#fdfbf7] border border-stone-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-[#8b5e3c] transition">
                            <option value="all">Semua Buku</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Batas Pemakaian</label>
                            <input type="number" name="usage_limit" placeholder="Kosongkan jika tanpa batas" class="w-full bg-[#fdfbf7] border border-stone-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-[#8b5e3c] transition">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Kadaluarsa</label>
                            <input type="date" name="expires_at" class="w-full bg-[#fdfbf7] border border-stone-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-[#8b5e3c] transition">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="openModal = false" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-[#3a261f] text-white hover:bg-[#2c1d18] shadow-sm transition">Simpan Voucher</button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Navigasi Bawah Konsisten -->
        <nav class="fixed bottom-0 left-0 right-0 bg-[#fdfbf7]/90 backdrop-blur-md border-t border-stone-200 px-6 py-3 flex justify-center gap-6 md:gap-12 items-center z-40 shadow-lg">
            <a href="{{ route('admin.transactions.index') }}" class="flex flex-col items-center px-3 {{ request()->routeIs('admin.transactions*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012-2h-2a2 2 0 01-2-2z" /></svg>
                <span class="text-[10px] font-medium mt-1">Transaksi</span>
            </a>

            <a href="{{ route('admin.vouchers.index') }}" class="flex flex-col items-center px-3 {{ request()->routeIs('admin.vouchers*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" /></svg>
                <span class="text-[10px] font-medium mt-1">Voucher</span>
            </a>

            <a href="{{ route('books.index') }}" class="flex flex-col items-center px-3 {{ request()->routeIs('books*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="text-[10px] font-medium mt-1">Koleksi</span>
            </a>

            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center px-3 {{ request()->routeIs('admin.dashboard*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="text-[10px] font-medium mt-1">Admin</span>
            </a>
        </nav>

    </div>

    <!-- Tambahkan Alpine.js untuk fungsionalitas popup modal -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>