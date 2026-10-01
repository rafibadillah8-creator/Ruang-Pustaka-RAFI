<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah E-Book Baru - Ruang Pustaka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen py-10">

    <div class="max-w-3xl mx-auto px-6">
        <!-- Tombol Kembali ke Dashboard Admin -->
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-[#4A2E18] hover:underline text-sm mb-6 transition font-medium">
            ← Kembali ke Dashboard Admin
        </a>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-[#0F172A] mb-2">Tambah E-Book Baru</h1>
            <p class="text-xs text-[#64748B] mb-6">Isi formulir di bawah ini untuk menambahkan koleksi buku baru ke katalog.</p>

            <!-- Kotak Pesan Error Validasi -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl">
                    <p class="font-bold mb-1">Terjadi kesalahan pengisian form:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Pilihan Kategori Buku + Fitur Tambah & Hapus Kategori -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kategori Buku (Pilih satu atau lebih)</label>
                        
                        <!-- Tombol Buka Input Tambah Kategori -->
                        <button type="button" onclick="toggleAddCategoryBox()" class="text-xs text-[#4A2E18] font-semibold hover:underline flex items-center gap-1">
                            + Tambah Kategori Baru
                        </button>
                    </div>

                    <!-- Kotak Form Tambah Kategori Cepat (Hidden by default) -->
                    <div id="add-category-container" class="hidden p-3 bg-[#FAF7F5] border border-[#EBE3DE] rounded-xl space-y-2">
                        <div class="flex gap-2">
                            <input type="text" id="new-category-name" placeholder="Nama kategori baru..." class="flex-1 bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-[#4A2E18]">
                            <button type="button" onclick="storeNewCategory()" class="bg-[#4A2E18] hover:bg-[#352011] text-white text-xs px-4 py-1.5 rounded-lg font-semibold transition">
                                Simpan
                            </button>
                        </div>
                        <p id="category-error" class="text-[11px] text-red-600 hidden"></p>
                    </div>

                    <!-- Daftar Kategori -->
                    <div id="category-list-container" class="max-h-48 overflow-y-auto bg-[#F8FAFC] border border-slate-200 rounded-xl p-4 space-y-2">
                        @forelse($categories as $cat)
                            <div class="flex items-center justify-between text-sm text-[#0F172A] hover:bg-slate-100/60 p-1.5 rounded-lg transition category-item" data-id="{{ $cat->id }}">
                                <label class="flex items-center space-x-3 cursor-pointer flex-1">
                                    <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                                        {{ (is_array(old('categories')) && in_array($cat->id, old('categories'))) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-[#4A2E18] focus:ring-[#4A2E18]">
                                    <span>{{ $cat->name }}</span>
                                </label>
                                <!-- Tombol Hapus Kategori -->
                                <button type="button" onclick="deleteCategory({{ $cat->id }}, '{{ $cat->name }}')" class="text-slate-400 hover:text-red-600 p-1 transition" title="Hapus Kategori">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p id="no-category-text" class="text-xs text-slate-500 text-center py-2">Belum ada kategori tersedia.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Judul Buku -->
                <div>
                    <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Judul Buku</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Masukkan judul buku" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#4A2E18]">
                </div>

                <!-- Penulis & Penerbit -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Penulis</label>
                        <input type="text" name="author" value="{{ old('author') }}" required placeholder="Nama penulis" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#4A2E18]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Penerbit</label>
                        <input type="text" name="publisher" value="{{ old('publisher') }}" placeholder="Nama penerbit" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#4A2E18]">
                    </div>
                </div>

                <!-- Deskripsi Buku -->
                <div>
                    <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Deskripsi Buku</label>
                    <textarea name="description" rows="3" placeholder="Tuliskan deskripsi singkat mengenai buku ini..." class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#4A2E18]">{{ old('description') }}</textarea>
                </div>

                <!-- Harga Buku -->
                <div>
                    <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" min="0" required placeholder="Isi 0 jika gratis" class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#0F172A] focus:outline-none focus:border-[#4A2E18]">
                    <span class="text-[11px] text-[#64748B] mt-1 block">Isi dengan angka 0 jika buku bersifat gratis.</span>
                </div>

                <!-- Upload File Gambar & PDF -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">Gambar Sampul / Cover</label>
                        <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#FAF7F5] file:text-[#4A2E18] hover:file:bg-[#F2ECE6]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#0F172A] uppercase mb-2">File E-Book (PDF)</label>
                        <input type="file" name="file_pdf" accept=".pdf" class="w-full text-xs text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#FAF7F5] file:text-[#4A2E18] hover:file:bg-[#F2ECE6]">
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-[#64748B] hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="bg-[#4A2E18] hover:bg-[#352011] text-white text-xs px-6 py-2.5 rounded-xl font-semibold transition shadow-sm">
                        Simpan Buku Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script AJAX Tambah & Hapus Kategori Instan -->
    <script>
        function toggleAddCategoryBox() {
            const box = document.getElementById('add-category-container');
            box.classList.toggle('hidden');
            if (!box.classList.contains('hidden')) {
                document.getElementById('new-category-name').focus();
            }
        }

        function storeNewCategory() {
            const nameInput = document.getElementById('new-category-name');
            const errorEl = document.getElementById('category-error');
            const categoryName = nameInput.value.trim();

            if (!categoryName) {
                errorEl.textContent = 'Nama kategori tidak boleh kosong.';
                errorEl.classList.remove('hidden');
                return;
            }

            fetch("{{ route('admin.categories.store.ajax') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name: categoryName })
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menambah kategori.');
                return data;
            })
            .then(data => {
                if (data.success) {
                    nameInput.value = '';
                    errorEl.classList.add('hidden');
                    toggleAddCategoryBox();

                    const noCatText = document.getElementById('no-category-text');
                    if (noCatText) noCatText.remove();

                    const listContainer = document.getElementById('category-list-container');
                    const newDiv = document.createElement('div');
                    newDiv.className = 'flex items-center justify-between text-sm text-[#0F172A] hover:bg-slate-100/60 p-1.5 rounded-lg transition category-item';
                    newDiv.setAttribute('data-id', data.category.id);
                    newDiv.innerHTML = `
                        <label class="flex items-center space-x-3 cursor-pointer flex-1">
                            <input type="checkbox" name="categories[]" value="${data.category.id}" checked class="rounded border-slate-300 text-[#4A2E18] focus:ring-[#4A2E18]">
                            <span>${data.category.name}</span>
                        </label>
                        <button type="button" onclick="deleteCategory(${data.category.id}, '${data.category.name}')" class="text-slate-400 hover:text-red-600 p-1 transition" title="Hapus Kategori">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    `;
                    listContainer.appendChild(newDiv);
                }
            })
            .catch(err => {
                errorEl.textContent = err.message;
                errorEl.classList.remove('hidden');
            });
        }

        function deleteCategory(catId, catName) {
            if (!confirm(`Yakin ingin menghapus kategori "${catName}"?`)) return;

            fetch(`/admin/categories/${catId}/ajax`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menghapus kategori.');
                return data;
            })
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`.category-item[data-id="${catId}"]`);
                    if (item) item.remove();
                }
            })
            .catch(err => {
                alert(err.message || 'Terjadi kesalahan saat menghapus kategori.');
            });
        }
    </script>
</body>
</html>