<nav class="border-b border-slate-200 bg-white px-6 py-4 flex justify-between items-center sticky top-0 z-50">
    <!-- Logo / Judul -->
    <h1 class="text-xl font-bold text-[#2563EB]">Ruang Pustaka</h1>
    
    <!-- Bagian Kanan Navbar -->
    <div class="flex items-center gap-3">
        
        <!-- TOMBOL SEARCH -->
        <a href="{{ route('books.index') }}" class="p-2 text-slate-500 hover:text-[#2563EB] hover:bg-slate-100 rounded-lg transition" title="Cari Buku">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </a>

        <!-- Nama User & Logout -->
        <span class="text-sm text-[#64748B] hidden md:inline">Selamat membaca, <strong class="text-[#0F172A]">{{ Auth::user()->name ?? 'User' }}</strong></span>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 px-3 py-1.5 rounded-lg font-semibold transition">
                Logout
            </button>
        </form>
    </div>
</nav>