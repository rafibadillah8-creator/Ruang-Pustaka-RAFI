<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#fdfbf7] text-[#3a261f] min-h-screen flex antialiased">
    <div class="w-full min-h-screen bg-white shadow-xl flex flex-col relative pb-24">
        
        <!-- Header -->
        <header class="px-8 py-5 flex items-center justify-between border-b border-stone-200 bg-[#fdfbf7]/80 backdrop-blur-md sticky top-0 z-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#c19b6c] flex items-center justify-center text-white font-bold text-lg shadow-md shadow-[#c19b6c]/50">RP</div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Admin Panel</h1>
                    <p class="text-[11px] text-slate-400">Kelola Transaksi Peminjaman</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-full transition">Keluar</button>
            </form>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-8 py-6 space-y-6">
            <!-- Banner -->
            <div class="bg-gradient-to-r from-[#3a261f] to-[#2c1d18] rounded-3xl p-8 text-white shadow-lg shadow-[#3a261f]/20">
                <span class="text-[10px] uppercase tracking-wider bg-[#c19b6c]/40 px-3 py-1 rounded-full font-bold">Laporan Keuangan</span>
                <h2 class="text-2xl font-bold mt-3 text-white">Data Transaksi Peminjaman</h2>
                <p class="text-[#e8dcc4] text-xs mt-1">Pantau semua aktivitas pendapatan dan riwayat transaksi perpustakaan.</p>
            </div>

            <!-- Kartu Statistik Pendapatan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Pendapatan Hari Ini -->
                <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#fdfbf7] text-[#8b5e3c] flex items-center justify-center font-bold text-xl border border-[#c19b6c]/30">
                        Hari
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Pendapatan Hari Ini</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-0.5">Rp {{ number_format($todayIncome, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Pendapatan Bulan Ini -->
                <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#fdfbf7] text-[#8b5e3c] flex items-center justify-center font-bold text-xl border border-[#c19b6c]/30">
                        Bulan
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Pendapatan Bulan Ini</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-0.5">Rp {{ number_format($monthIncome, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <!-- Total Pendapatan -->
                <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#fdfbf7] text-[#8b5e3c] flex items-center justify-center font-bold text-xl border border-[#c19b6c]/30">
                        Total
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Total Pendapatan</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-0.5">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <!-- Filter & Aksi Tambahan -->
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <input type="text" placeholder="Cari transaksi atau judul buku..." class="w-full text-xs bg-[#fdfbf7] border border-stone-200 rounded-full py-2.5 pl-4 pr-10 focus:outline-none focus:border-[#8b5e3c] transition">
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                    <a href="{{ route('admin.transactions.export') }}" target="_blank" class="px-4 py-2.5 text-xs font-semibold text-[#3a261f] bg-[#fdfbf7] hover:bg-[#e8dcc4] border border-[#c19b6c]/30 rounded-full transition text-center">Export Laporan</a>
                </div>
            </div>

            <!-- Tabel Transaksi / Riwayat -->
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-4">Riwayat Transaksi Masuk</h3>
                
                @if(isset($transactions) && $transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-stone-100 text-[11px] text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 px-4 font-semibold">No</th>
                                    <th class="py-3 px-4 font-semibold">ID Transaksi</th>
                                    <th class="py-3 px-4 font-semibold">Judul Buku / Item</th>
                                    <th class="py-3 px-4 font-semibold">Penulis</th>
                                    <th class="py-3 px-4 font-semibold">Peminjam</th>
                                    <th class="py-3 px-4 font-semibold">Tanggal</th>
                                    <th class="py-3 px-4 font-semibold text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 text-xs text-slate-600">
                                @foreach($transactions as $index => $trx)
                                    <tr class="hover:bg-[#fdfbf7]/50 transition">
                                        <td class="py-3.5 px-4 font-medium text-slate-400">{{ $index + 1 }}</td>
                                        <td class="py-3.5 px-4 font-mono text-slate-500">#TRX-{{ $trx->id ?? ($index + 1001) }}</td>
                                        <td class="py-3.5 px-4 font-bold text-slate-800">{{ $trx->title }}</td>
                                        <td class="py-3.5 px-4 text-slate-500">{{ $trx->author }}</td>
                                        <td class="py-3.5 px-4 text-slate-600">{{ $trx->user->name ?? 'Anggota Perpustakaan' }}</td>
                                        <td class="py-3.5 px-4 text-slate-400">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                                        <td class="py-3.5 px-4 text-right font-bold text-[#8b5e3c]">Rp {{ number_format($trx->price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 text-slate-400 text-xs">
                        Belum ada data transaksi yang tercatat.
                    </div>
                @endif
            </div>
        </main>

        <!-- Navigasi Bawah Konsisten (Urutan: Transaksi, Voucher, Koleksi, Admin) -->
        <nav class="fixed bottom-0 left-0 right-0 bg-[#fdfbf7]/90 backdrop-blur-md border-t border-stone-200 px-6 py-3 flex justify-center gap-6 md:gap-12 items-center z-50 shadow-lg">
            <a href="{{ route('admin.transactions.index') }}" class="flex flex-col items-center px-3 {{ request()->routeIs('admin.transactions*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
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
</body>
</html>