<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Semua Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Semua Transaksi Ruang Pustaka</h2>
    <p><strong>Admin:</strong> {{ $user->name }}<br>
       <strong>Tanggal Laporan:</strong> {{ date('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Judul Buku</th>
                <th>Tanggal Transaksi</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $t)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ optional($t->user)->name ?? 'Anggota Perpustakaan' }}</td>
                <td>{{ $t->title ?? 'Buku ID: ' . $t->book_id }}</td>
                <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                <td>Rp {{ number_format($t->price ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;">Belum ada riwayat transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
