<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Buku Pustaka & Kelola Katalog - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#fdfbf7] text-[#3a261f] min-h-screen flex antialiased">

    @php
        $books = $books ?? collect();
    @endphp

    <!-- Container Utama Full Layar -->
    <div class="w-full min-h-screen bg-white shadow-xl flex flex-col relative pb-24">
        
        <!-- Header Atas (Profil & Selamat Datang) -->
        <header class="px-8 py-5 flex items-center justify-between border-b border-stone-200 bg-[#fdfbf7]/85 backdrop-blur-md sticky top-0 z-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#c19b6c] flex items-center justify-center text-white font-bold text-lg shadow-md shadow-[#c19b6c]/50">
                    RP
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Admin Panel</h1>
                    <p class="text-[11px] text-slate-400">Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}</p>
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
            <div class="bg-gradient-to-r from-[#3a261f] to-[#2c1d18] rounded-3xl p-8 text-white shadow-lg shadow-[#3a261f]/20">
                <span class="text-[10px] uppercase tracking-wider bg-[#c19b6c]/40 px-3 py-1 rounded-full font-bold">Kelola Katalog</span>
                <h2 class="text-2xl font-bold mt-3">Daftar Buku Pustaka</h2>
                <p class="text-[#e8dcc4] text-xs mt-1">Tambah, Ubah, Hapus Koleksi Buku.</p>
                
                <div class="flex flex-wrap gap-3 mt-5">
                    <a href="{{ route('admin.books.create') }}" class="inline-flex items-center gap-2 bg-[#fdfbf7] text-[#3a261f] px-4 py-2.5 rounded-2xl font-semibold text-xs shadow hover:bg-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Buku Baru
                    </a>

                    <a href="{{ route('admin.vouchers.index') }}" class="inline-flex items-center gap-2 bg-[#5e4033]/40 text-white border border-white/30 px-4 py-2.5 rounded-2xl font-semibold text-xs shadow hover:bg-[#5e4033]/60 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Kelola Voucher
                    </a>
                </div>
            </div>

            <!-- Form Pencarian & Total Buku -->
            <div class="space-y-4">
                <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau penulis..." class="w-full bg-white border border-stone-200 rounded-2xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-[#8b5e3c] shadow-sm">
                    </div>
                    <button type="submit" class="bg-[#3a261f] text-white px-6 py-3 rounded-2xl text-xs font-semibold hover:bg-[#2c1d18] transition shadow-sm">Cari</button>
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
                                    @php
                                        $categoryNames = collect();
                                        if ($book->relationLoaded('categories') && $book->categories->isNotEmpty()) {
                                            $categoryNames = $book->categories->pluck('name');
                                        } elseif (method_exists($book, 'categories') && $book->categories()->exists()) {
                                            $categoryNames = $book->categories->pluck('name');
                                        } else {
                                            $rawCategory = $book->category ?? null;
                                            if (is_string($rawCategory)) {
                                                $decoded = json_decode($rawCategory, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    $categoryNames = collect($decoded);
                                                } else {
                                                    $categoryNames = collect([$rawCategory]);
                                                }
                                            } elseif (is_array($rawCategory)) {
                                                $categoryNames = collect($rawCategory);
                                            } elseif (is_object($rawCategory)) {
                                                $categoryNames = collect([$rawCategory->name ?? '-']);
                                            }
                                        }
                                        if ($categoryNames->isEmpty()) {
                                            $categoryNames = collect(['-']);
                                        }
                                    @endphp
                                    <tr>
                                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $book->title }}</td>
                                        <td class="py-3.5 px-4 text-slate-600">{{ $book->author }}</td>
                                        <td class="py-3.5 px-4 text-slate-500">
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($categoryNames as $catName)
                                                    <span class="inline-block bg-[#fdfbf7] text-[#8b5e3c] px-2.5 py-1 rounded-md font-semibold border border-[#c19b6c]/30">
                                                        {{ $catName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center space-x-2">
                                            <button type="button" onclick="openEditModal({{ $book->id }})" class="text-[#8b5e3c] font-semibold hover:underline">Edit</button>
                                            <span class="text-slate-300">|</span>
                                            <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus buku ini?');">
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
        <nav class="fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-md border-t border-slate-100 px-6 py-3 flex justify-center gap-6 md:gap-12 items-center z-50 shadow-lg">
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

    <!-- Modal Popup Edit Buku -->
    <div id="editBookModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 md:p-8 shadow-xl relative my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-xl font-bold text-slate-900">Edit E-Book</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <div id="editModalLoading" class="text-center py-10 text-slate-500 text-xs">
                Memuat data...
            </div>

            <form id="editBookForm" method="POST" enctype="multipart/form-data" class="space-y-4 hidden">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_book_id">

                <!-- Kategori Buku -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kategori Buku</label>
                    <div id="edit-category-container" class="max-h-40 overflow-y-auto bg-[#F8FAFC] border border-slate-200 rounded-xl p-3 space-y-2">
                        <!-- Dinamis terisi via AJAX -->
                    </div>
                </div>

                <!-- Judul Buku -->
                <div>
                    <label class="block text-xs font-semibold text-slate-900 uppercase mb-1">Judul Buku</label>
                    <input type="text" id="edit_title" name="title" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#8b5e3c]">
                </div>

                <!-- Penulis & Penerbit -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-900 uppercase mb-1">Penulis</label>
                        <input type="text" id="edit_author" name="author" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#8b5e3c]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-900 uppercase mb-1">Penerbit</label>
                        <input type="text" id="edit_publisher" name="publisher" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#8b5e3c]">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-900 uppercase mb-1">Deskripsi</label>
                    <textarea id="edit_description" name="description" rows="3" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#8b5e3c]"></textarea>
                </div>

                <!-- Harga -->
                <div>
                    <label class="block text-xs font-semibold text-slate-900 uppercase mb-1">Harga (Rp)</label>
                    <input type="number" id="edit_price" name="price" min="0" required class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#8b5e3c]">
                </div>

                <!-- File Cover & PDF -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-900 uppercase mb-1">Ganti Cover (Opsional)</label>
                        <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-slate-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-900 uppercase mb-1">Ganti File PDF (Opsional)</label>
                        <input type="file" name="file_pdf" accept=".pdf" class="w-full text-xs text-slate-500">
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl border text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="bg-[#3a261f] hover:bg-[#2c1d18] text-white text-xs px-5 py-2 rounded-xl font-semibold shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script AJAX untuk Modal Edit -->
    <script>
        function openEditModal(bookId) {
            const modal = document.getElementById('editBookModal');
            const loading = document.getElementById('editModalLoading');
            const form = document.getElementById('editBookForm');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            form.classList.add('hidden');

            // Fetch data buku via AJAX
            fetch(`/admin/books/${bookId}/json`)
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const book = data.book;
                        const allCategories = data.categories;
                        const bookCategoryIds = book.categories.map(c => c.id);

                        document.getElementById('edit_book_id').value = book.id;
                        document.getElementById('edit_title').value = book.title;
                        document.getElementById('edit_author').value = book.author;
                        document.getElementById('edit_publisher').value = book.publisher || '';
                        document.getElementById('edit_description').value = book.description || '';
                        document.getElementById('edit_price').value = book.price;

                        // Render checkbox kategori
                        let catHtml = '';
                        allCategories.forEach(cat => {
                            let checked = bookCategoryIds.includes(cat.id) ? 'checked' : '';
                            catHtml += `
                                <label class="flex items-center space-x-3 cursor-pointer text-sm">
                                    <input type="checkbox" name="categories[]" value="${cat.id}" ${checked} class="rounded border-slate-300 text-[#8b5e3c] focus:ring-[#8b5e3c]">
                                    <span class="text-slate-800">${cat.name}</span>
                                </label>
                            `;
                        });
                        document.getElementById('edit-category-container').innerHTML = catHtml;

                        // Set Action URL form
                        form.action = `/admin/books/${book.id}`;

                        loading.classList.add('hidden');
                        form.classList.remove('hidden');
                    }
                })
                .catch(err => {
                    alert('Gagal mengambil data buku.');
                    closeEditModal();
                });
        }

        function closeEditModal() {
            document.getElementById('editBookModal').classList.add('hidden');
        }

        // Handle Submit Form Edit via AJAX
        document.getElementById('editBookForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const actionUrl = this.action;

            fetch(actionUrl, {
                method: 'POST', // Laravel tetap pakai POST dengan _method = PUT di dalam formData
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if(!res.ok) throw new Error(data.message || 'Terjadi kesalahan.');
                return data;
            })
            .then(data => {
                if(data.success) {
                    alert('Buku berhasil diperbarui!');
                    location.reload(); // Refresh halaman untuk melihat hasil
                }
            })
            .catch(err => {
                alert(err.message || 'Gagal memperbarui buku.');
            });
        });
    </script>
</body>
</html>