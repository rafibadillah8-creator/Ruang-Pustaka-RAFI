<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembelian - {{ $book->title ?? 'Buku' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Midtrans Snap JS Script -->
    <script type="text/javascript" 
            src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="Mid-client-LjMP1zn4XEDLimPA"></script>
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] min-h-screen py-12 px-6 antialiased flex items-center justify-center">

    <div class="max-w-md w-full bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
        <div>
            <a href="{{ route('books.show', $book->id) }}" class="text-xs font-semibold text-slate-400 hover:text-blue-600 transition inline-flex items-center gap-1 mb-4">
                &larr; Batal & Kembali
            </a>
            <h1 class="text-xl font-bold text-slate-900">Konfirmasi Pembelian Buku</h1>
            <p class="text-xs text-slate-500 mt-1">Selesaikan pembayaran melalui Midtrans untuk langsung membuka akses membaca buku ini.</p>
        </div>

        <!-- Ringkasan Buku -->
        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 flex gap-4 items-center">
            <div class="w-16 h-20 bg-slate-200 rounded-xl overflow-hidden shrink-0 flex items-center justify-center">
                @if(!empty($book->cover_image))
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="" class="w-full h-full object-cover">
                @else
                    <span class="text-[9px] text-slate-400">No Cover</span>
                @endif
            </div>
            <div>
                <h3 class="font-bold text-xs text-slate-900 line-clamp-1">{{ $book->title }}</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">{{ $book->author }}</p>
                <div class="text-xs font-bold text-blue-600 mt-2">
                    Rp {{ number_format($book->price ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Pembayaran Midtrans -->
        <button id="pay-button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-2xl font-semibold text-xs shadow-sm transition flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            Bayar dengan Midtrans
        </button>
    </div>

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            // Ambil kode voucher dari input atau session jika ada
            let voucherCode = document.getElementById('voucher_code') ? document.getElementById('voucher_code').value : '';

            // Mengirim request ke rute proses beli dengan method POST dan menyertakan data voucher
            fetch('/books/{{ $book->id }}/buy', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    voucher_code: voucherCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            // Konfirmasi balik ke server setelah pembayaran sukses agar used_count voucher bertambah
                            fetch('/books/{{ $book->id }}/buy', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    voucher_code: voucherCode,
                                    payment_completed: true
                                })
                            }).then(() => {
                                alert("Pembayaran berhasil!");
                                window.location.href = "{{ route('books.show', $book->id) }}";
                            });
                        },
                        onPending: function(result){
                            alert("Menunggu pembayaran Anda!");
                            console.log(result);
                        },
                        onError: function(result){
                            alert("Pembayaran gagal!");
                            console.log(result);
                        },
                        onClose: function(){
                            alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                        }
                    });
                } else {
                    alert('Gagal mendapatkan token: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        };
    </script>
</body>
</html>