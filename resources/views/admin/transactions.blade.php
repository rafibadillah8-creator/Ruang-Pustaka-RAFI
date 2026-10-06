<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .bar {
            transition: height .7s cubic-bezier(.34, 1.56, .64, 1), background-color .2s;
        }

        .bar-col {
            cursor: pointer;
        }

        .bar-col:hover .bar,
        .bar-col.active .bar {
            background-color: #8b5e3c;
        }
    </style>
</head>

<body class="bg-[#fdfbf7] text-[#3a261f] min-h-screen flex antialiased">
    <div class="w-full min-h-screen bg-white shadow-xl flex flex-col relative pb-24">

        <!-- Header -->
        <header
            class="px-8 py-5 flex items-center justify-between border-b border-stone-200 bg-[#fdfbf7]/80 backdrop-blur-md sticky top-0 z-50">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-[#c19b6c] flex items-center justify-center text-white font-bold text-lg shadow-md shadow-[#c19b6c]/50">
                    RP</div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Admin Panel</h1>
                    <p class="text-[11px] text-slate-400">Kelola Transaksi Peminjaman</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-full transition">Keluar</button>
            </form>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-8 py-6 space-y-6">
            <!-- Banner -->
            <div
                class="bg-gradient-to-r from-[#3a261f] to-[#2c1d18] rounded-3xl p-8 text-white shadow-lg shadow-[#3a261f]/20">
                <span
                    class="text-[10px] uppercase tracking-wider bg-[#c19b6c]/40 px-3 py-1 rounded-full font-bold">Laporan
                    Keuangan</span>
                <h2 class="text-2xl font-bold mt-3 text-white">Data Transaksi</h2>
                <p class="text-[#e8dcc4] text-xs mt-1">Pantau semua aktivitas pendapatan dan riwayat transaksi
                    perpustakaan.</p>
            </div>

            <!-- Kartu Statistik Pendapatan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Pendapatan Hari Ini -->
                <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[#fdfbf7] text-[#8b5e3c] flex items-center justify-center font-bold text-xl border border-[#c19b6c]/30">
                        Hari
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Pendapatan Hari Ini</p>
                        <h3 id="stat-today" class="text-xl font-bold text-slate-800 mt-0.5">Rp
                            {{ number_format($todayIncome, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>

                <!-- Pendapatan Bulan Ini -->
                <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[#fdfbf7] text-[#8b5e3c] flex items-center justify-center font-bold text-xl border border-[#c19b6c]/30">
                        Bulan
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Pendapatan Bulan Ini</p>
                        <h3 id="stat-month" class="text-xl font-bold text-slate-800 mt-0.5">Rp
                            {{ number_format($monthIncome, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>

                <!-- Total Pendapatan -->
                <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[#fdfbf7] text-[#8b5e3c] flex items-center justify-center font-bold text-xl border border-[#c19b6c]/30">
                        Total
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Total Pendapatan</p>
                        <h3 id="stat-total" class="text-xl font-bold text-slate-800 mt-0.5">Rp
                            {{ number_format($totalIncome, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>


            <!-- Grafik Pendapatan Bulanan -->
            <div class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Pendapatan Bulanan</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                            <span id="live-dot"
                                class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span id="live-status">Memuat data...</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="year-prev" type="button"
                            class="w-8 h-8 rounded-full border border-stone-200 text-slate-500 hover:bg-[#fdfbf7] transition text-sm">&lsaquo;</button>
                        <span id="year-label"
                            class="text-xs font-bold text-slate-700 w-12 text-center">{{ now()->year }}</span>
                        <button id="year-next" type="button"
                            class="w-8 h-8 rounded-full border border-stone-200 text-slate-500 hover:bg-[#fdfbf7] transition text-sm">&rsaquo;</button>
                    </div>
                </div>

                <div id="chart-wrap" class="relative h-72 select-none">
                    <!-- garis grid + label sumbu Y -->
                    <div id="chart-grid" class="absolute inset-x-0 top-0 bottom-8 pointer-events-none"></div>
                    <!-- batang -->
                    <div id="chart-bars" class="absolute left-12 right-0 top-0 bottom-8 flex items-end gap-2 sm:gap-3">
                    </div>
                    <!-- label bulan -->
                    <div id="chart-labels"
                        class="absolute left-12 right-0 bottom-0 h-8 flex gap-2 sm:gap-3 items-center"></div>
                    <!-- tooltip -->
                    <div id="chart-tip"
                        class="hidden absolute z-10 pointer-events-none bg-[#3a261f] text-white rounded-xl px-3 py-2 shadow-lg text-[11px] min-w-[150px]">
                    </div>
                </div>
            </div>

            <!-- Filter & Aksi Tambahan -->
            <div
                class="bg-white border border-stone-200 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <input type="text" placeholder="Cari transaksi atau judul buku..."
                            class="w-full text-xs bg-[#fdfbf7] border border-stone-200 rounded-full py-2.5 pl-4 pr-10 focus:outline-none focus:border-[#8b5e3c] transition">
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                    <a href="{{ route('admin.transactions.export') }}" target="_blank"
                        class="px-4 py-2.5 text-xs font-semibold text-[#3a261f] bg-[#fdfbf7] hover:bg-[#e8dcc4] border border-[#c19b6c]/30 rounded-full transition text-center">Export
                        Laporan</a>
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
                                        <td class="py-3.5 px-4 font-mono text-slate-500">#TRX-{{ $trx->id ?? ($index + 1001) }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-slate-800">{{ $trx->title }}</td>
                                        <td class="py-3.5 px-4 text-slate-500">{{ $trx->author }}</td>
                                        <td class="py-3.5 px-4 text-slate-600">{{ $trx->user->name ?? 'Anggota Perpustakaan' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-400">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                                        <td class="py-3.5 px-4 text-right font-bold text-[#8b5e3c]">Rp
                                            {{ number_format($trx->price, 0, ',', '.') }}
                                        </td>
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
        <nav
            class="fixed bottom-0 left-0 right-0 bg-[#fdfbf7]/90 backdrop-blur-md border-t border-stone-200 px-6 py-3 flex justify-center gap-6 md:gap-12 items-center z-50 shadow-lg">
            <a href="{{ route('admin.transactions.index') }}"
                class="flex flex-col items-center px-3 {{ request()->routeIs('admin.transactions*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] font-medium mt-1">Transaksi</span>
            </a>

            <a href="{{ route('admin.vouchers.index') }}"
                class="flex flex-col items-center px-3 {{ request()->routeIs('admin.vouchers*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                </svg>
                <span class="text-[10px] font-medium mt-1">Voucher</span>
            </a>

            <a href="{{ route('books.index') }}"
                class="flex flex-col items-center px-3 {{ request()->routeIs('books*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
                <span class="text-[10px] font-medium mt-1">Koleksi</span>
            </a>

            <a href="{{ route('admin.dashboard') }}"
                class="flex flex-col items-center px-3 {{ request()->routeIs('admin.dashboard*') ? 'text-[#8b5e3c]' : 'text-slate-400 hover:text-[#8b5e3c]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-[10px] font-medium mt-1">Admin</span>
            </a>
        </nav>
    </div>

    <script>
        (function () {
            const DATA_URL = "{{ \Illuminate\Support\Facades\Route::has('admin.transactions.chart') ? route('admin.transactions.chart') : url('/admin/transactions/chart-data') }}";
            const POLL_MS = 10000; // refresh otomatis tiap 10 detik
            const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const BULAN_FULL = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const NOW_YEAR = new Date().getFullYear();

            let year = NOW_YEAR;
            let months = Array.from({ length: 12 }, (_, i) => ({ month: i + 1, total: 0, count: 0 }));
            let timer = null;

            const $ = id => document.getElementById(id);
            const rupiah = n => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
            const compact = n => {
                if (n >= 1e9) return (n / 1e9).toFixed(1).replace('.0', '') + 'M';
                if (n >= 1e6) return (n / 1e6).toFixed(1).replace('.0', '') + 'jt';
                if (n >= 1e3) return (n / 1e3).toFixed(0) + 'k';
                return String(n);
            };
            // pembulatan skala sumbu Y ke angka "cantik"
            const niceMax = v => {
                if (v <= 0) return 100000;
                const p = Math.pow(10, Math.floor(Math.log10(v)));
                const f = v / p;
                return (f <= 1 ? 1 : f <= 2 ? 2 : f <= 5 ? 5 : 10) * p;
            };

            function buildSkeleton() {
                $('chart-bars').innerHTML = BULAN.map((b, i) => `
                <div class="bar-col flex-1 h-full flex items-end" data-i="${i}">
                    <div class="bar w-full rounded-t-lg bg-[#e8dcc4]" style="height:2px"></div>
                </div>`).join('');
                $('chart-labels').innerHTML = BULAN.map(b =>
                    `<span class="flex-1 text-center text-[10px] text-slate-400 font-medium">${b}</span>`).join('');

                document.querySelectorAll('.bar-col').forEach(col => {
                    col.addEventListener('mouseenter', () => showTip(+col.dataset.i, col));
                    col.addEventListener('mousemove', () => showTip(+col.dataset.i, col));
                    col.addEventListener('mouseleave', hideTip);
                    col.addEventListener('click', () => showTip(+col.dataset.i, col));
                });
            }

            function render() {
                const max = niceMax(Math.max(...months.map(m => m.total)));

                // grid + sumbu Y (4 garis)
                const steps = 4;
                $('chart-grid').innerHTML = Array.from({ length: steps + 1 }, (_, k) => {
                    const val = max - (max / steps) * k;
                    const top = (k / steps) * 100;
                    return `<div class="absolute left-0 right-0 flex items-center" style="top:${top}%;transform:translateY(-50%)">
                            <span class="w-10 text-[10px] text-slate-400 text-right pr-2">${compact(val)}</span>
                            <span class="flex-1 border-t border-dashed border-stone-200"></span>
                        </div>`;
                }).join('');

                document.querySelectorAll('.bar').forEach((el, i) => {
                    const m = months[i];
                    const isFuture = year === NOW_YEAR && m.month > new Date().getMonth() + 1;
                    const pct = max ? (m.total / max) * 100 : 0;
                    el.style.height = (m.total > 0 ? Math.max(pct, 2) : 0) + '%';
                    el.style.minHeight = '2px';
                    el.style.opacity = isFuture ? '.35' : '1';
                    // bulan berjalan diberi warna berbeda
                    const isCurrent = year === NOW_YEAR && m.month === new Date().getMonth() + 1;
                    el.style.backgroundColor = '';
                    el.classList.toggle('bg-[#c19b6c]', isCurrent);
                    el.classList.toggle('bg-[#e8dcc4]', !isCurrent);
                });
            }

            function showTip(i, col) {
                const m = months[i];
                const prev = i > 0 ? months[i - 1].total : null;
                let delta = '';
                if (prev !== null && prev > 0) {
                    const d = ((m.total - prev) / prev) * 100;
                    delta = `<div class="${d >= 0 ? 'text-emerald-300' : 'text-rose-300'} mt-1">${d >= 0 ? '▲' : '▼'} ${Math.abs(d).toFixed(0)}% vs ${BULAN[i - 1]}</div>`;
                }
                const tip = $('chart-tip');
                tip.innerHTML = `
                <div class="font-bold mb-1">${BULAN_FULL[i]} ${year}</div>
                <div class="flex justify-between gap-4"><span class="text-[#e8dcc4]">Pendapatan</span><span class="font-bold">${rupiah(m.total)}</span></div>
                <div class="flex justify-between gap-4"><span class="text-[#e8dcc4]">Transaksi</span><span class="font-bold">${m.count}</span></div>${delta}`;
                tip.classList.remove('hidden');

                const wrap = $('chart-wrap').getBoundingClientRect();
                const c = col.getBoundingClientRect();
                let left = c.left - wrap.left + c.width / 2 - tip.offsetWidth / 2;
                left = Math.max(0, Math.min(left, wrap.width - tip.offsetWidth));
                tip.style.left = left + 'px';
                tip.style.top = '0px';
                document.querySelectorAll('.bar-col').forEach(x => x.classList.remove('active'));
                col.classList.add('active');
            }
            function hideTip() {
                $('chart-tip').classList.add('hidden');
                document.querySelectorAll('.bar-col').forEach(x => x.classList.remove('active'));
            }

            async function load() {
                try {
                    const res = await fetch(`${DATA_URL}?year=${year}`, { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                    if (!res.ok) throw new Error(res.status);
                    const json = await res.json();
                    months = json.months;
                    render();

                    // update kartu statistik secara real-time
                    if (json.stats) {
                        $('stat-today').textContent = rupiah(json.stats.today);
                        $('stat-month').textContent = rupiah(json.stats.month);
                        $('stat-total').textContent = rupiah(json.stats.total);
                    }
                    $('live-dot').className = 'inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse';
                    $('live-status').textContent = 'Live · diperbarui ' + new Date().toLocaleTimeString('id-ID');
                } catch (e) {
                    $('live-dot').className = 'inline-block w-1.5 h-1.5 rounded-full bg-rose-500';
                    $('live-status').textContent = 'Gagal memuat data, mencoba lagi...';
                }
            }

            function startPolling() { stopPolling(); timer = setInterval(load, POLL_MS); }
            function stopPolling() { if (timer) clearInterval(timer); timer = null; }

            function setYear(y) {
                year = y;
                $('year-label').textContent = y;
                $('year-next').disabled = y >= NOW_YEAR;
                $('year-next').classList.toggle('opacity-40', y >= NOW_YEAR);
                load();
            }
            $('year-prev').addEventListener('click', () => setYear(year - 1));
            $('year-next').addEventListener('click', () => { if (year < NOW_YEAR) setYear(year + 1); });

            // hemat resource: berhenti polling saat tab tidak aktif
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) stopPolling(); else { load(); startPolling(); }
            });

            buildSkeleton();
            setYear(NOW_YEAR);
            startPolling();
        })();
    </script>
</body>

</html>