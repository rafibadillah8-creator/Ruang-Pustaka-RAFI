<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionExportController extends Controller
{
    public function exportPdf()
    {
        $transactions = Transaction::latest()->get();
        $user = Auth::user();

        $pdf = Pdf::loadView('user.transactions.export', compact('transactions', 'user'));
        return $pdf->download('Riwayat_Semua_Transaksi_Ruang_Pustaka_' . time() . '.pdf');
    }
}
