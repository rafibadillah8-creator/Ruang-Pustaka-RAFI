<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku Pustaka & Kelola Katalog - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen flex antialiased">

    @php
        $books = $books ?? collect();
    @endphp

    <!-- Container Utama Full Layar -->
    <div class="w-full min-h-screen bg-white shadow-xl flex flex-col relative pb-24">
        
        <!-- Header Atas (Profil & Selamat Datang) -->
        <header class="px-8 py-5 flex items-center justify-between border-b border-slate-100 bg-white/80 backdrop-blur-md sticky top-0 z-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-blue-200">
                    RP
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Admin Panel</h1>
                    <p class="text-[11px] text-slate-400">Halo, {{ Auth::user()->name ?? 'Admin' }}</p>
                </div>
            </div>

            <!-- Tombol Logout Cepat -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="text-xs bg-red-50 text-red-600 px-3.5 py-1.5 rounded-full font-semibold hover:bg-red-100 transition">
                    Keluar
                </button>
            </form>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-8 py-6 space-y-6">
            
            <!-- Flash Alert Message -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Banner Kelola Katalog -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-8 text-white shadow-lg shadow-blue-200">
                <span class="text-[10px] uppercase tracking-wider bg-blue-500/50 px-3 py-1 rounded-full font-bold">Kelola Katalog</span>
                <h2 class="text-2xl font-bold mt-3">Daftar Buku Pustaka</h2>
                <p class="text-blue-100 text-xs mt-1">Tambah, ubah, atau hapus koleksi buku dengan mudah.</p>
                
                <a href="{{ route('books.create') }}" class="mt-5 inline-flex items-center gap-2 bg-white text-blue-600 px-4 py-2.5 rounded-2xl font-semibold text-xs shadow hover:bg-blue-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Buku Baru
                </a>
            </div>

            <!-- Form Pencarian & Total Buku -->
            <div class="space-y-4">
                <form action="{{ route('books.index') }}" method="GET" class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau penulis..." class="w-full bg-white border border-slate-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    </div>
                    <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-2xl text-xs font-semibold hover:bg-slate-800 transition shadow-sm">Cari</button>
                </form>

                <div class="flex justify-between items-center pt-2">
                    <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase">Koleksi Buku</h3>
                    <span class="text-xs font-semibold text-slate-500">{{ method_exists($books, 'total') ? $books->total() : count($books) }} Total Buku</span>
                </div>
            </div>

            <!-- List / Grid / Tabel Koleksi Buku -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                @if(count($books) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-3 px-4">Judul Buku</th>
                                    <th class="py-3 px-4">Penulis</th>
                                    <th class="py-3 px-4">Kategori</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs divide-y divide-slate-50 text-slate-700">
                                @foreach($books as $book)
                                    <tr>
                                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $book->title }}</td>
                                        <td class="py-3.5 px-4 text-slate-600">{{ $book->author }}</td>
                                        <!-- Perbaikan pemanggilan kolom category string -->
                                        <td class="py-3.5 px-4 text-slate-500">{{ $book->category ?? '-' }}</td>
                                        <td class="py-3.5 px-4 text-center space-x-2">
                                            <a href="{{ route('books.edit', $book->id) }}" class="text-blue-600 font-semibold hover:underline">Edit</a>
                                            <span class="text-slate-300">|</span>
                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 font-semibold hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if(method_exists($books, 'links'))
                        <div class="pt-4">
                            {{ $books->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 text-slate-400 text-xs">
                        Belum ada koleksi buku yang tersedia.
                    </div>
                @endif
            </div>

        </main>

        <!-- Navigasi Bawah Konsisten -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-slate-100 px-6 py-3 flex justify-center gap-12 items-center z-50 shadow-lg">
            <!-- Menu Transaksi -->
            <a href="{{ route('admin.transactions.index') }}" class="flex flex-col items-center px-4 {{ request()->routeIs('admin.transactions*') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] font-medium mt-1">Transaksi</span>
            </a>

            <!-- Menu Koleksi -->
            <a href="{{ route('books.index') }}" class="flex flex-col items-center px-4 {{ request()->routeIs('books.index') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="text-[10px] font-medium mt-1">Koleksi</span>
            </a>

            <!-- Menu Admin -->
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center px-4 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="text-[10px] font-medium mt-1">Admin</span>
            </a>
        </nav>

    </div>

</body>
</html>