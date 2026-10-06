<?php

namespace App\Services;

use App\Models\PaymentOrder;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentOrderFulfillment
{
    public function applyStatus(PaymentOrder $paymentOrder, array $status): void
    {
        $transactionStatus = $status['transaction_status'] ?? '';
        $fraudStatus = $status['fraud_status'] ?? 'accept';
        $grossAmount = $status['gross_amount'] ?? null;

        if (!$this->amountMatches($paymentOrder->price, $grossAmount)) {
            throw new InvalidArgumentException('Nominal notifikasi Midtrans tidak cocok dengan pesanan.');
        }

        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            $this->markPaid($paymentOrder);
            return;
        }

        if (in_array($transactionStatus, ['cancel', 'deny', 'expire', 'failure'], true)) {
            DB::transaction(function () use ($paymentOrder) {
                $lockedOrder = PaymentOrder::whereKey($paymentOrder->id)->lockForUpdate()->firstOrFail();

                if ($lockedOrder->status !== 'paid') {
                    $lockedOrder->update(['status' => 'failed']);
                }
            });
        }
    }

    private function markPaid(PaymentOrder $paymentOrder): void
    {
        DB::transaction(function () use ($paymentOrder) {
            $lockedOrder = PaymentOrder::whereKey($paymentOrder->id)
                ->with('book')
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status === 'paid') {
                return;
            }

            $transaction = Transaction::firstOrCreate(
                [
                    'user_id' => $lockedOrder->user_id,
                    'book_id' => $lockedOrder->book_id,
                ],
                [
                    'title' => trim($lockedOrder->book->title),
                    'author' => $lockedOrder->book->author,
                    'price' => $lockedOrder->price,
                ]
            );

            if ($transaction->wasRecentlyCreated && $lockedOrder->voucher_code) {
                Voucher::where('code', $lockedOrder->voucher_code)->increment('used_count');
            }

            $lockedOrder->update(['status' => 'paid']);
        });
    }

    private function amountMatches($expected, $received): bool
    {
        return is_numeric($received)
            && abs((float) $expected - (float) $received) < 0.005;
    }
}
