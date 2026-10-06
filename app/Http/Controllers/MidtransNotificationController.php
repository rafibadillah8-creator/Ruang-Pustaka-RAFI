<?php

namespace App\Http\Controllers;

use App\Models\PaymentOrder;
use App\Services\PaymentOrderFulfillment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, PaymentOrderFulfillment $fulfillment): JsonResponse
    {
        $notification = $request->validate([
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'numeric'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'fraud_status' => ['nullable', 'string'],
        ]);

        $serverKey = trim((string) config('services.midtrans.server_key'));
        $expectedSignature = hash(
            'sha512',
            $notification['order_id']
                . $notification['status_code']
                . $notification['gross_amount']
                . $serverKey
        );

        if ($serverKey === '' || !hash_equals($expectedSignature, $notification['signature_key'])) {
            Log::warning('Rejected Midtrans notification with an invalid signature.', [
                'order_id' => $notification['order_id'],
            ]);

            return response()->json(['message' => 'Signature not valid.'], 403);
        }

        $paymentOrder = PaymentOrder::where('order_id', $notification['order_id'])->first();

        if (!$paymentOrder) {
            Log::warning('Midtrans notification does not match a local payment order.', [
                'order_id' => $notification['order_id'],
            ]);

            return response()->json(['message' => 'Payment order not found.'], 404);
        }

        try {
            $fulfillment->applyStatus($paymentOrder, $notification);
        } catch (\InvalidArgumentException $exception) {
            Log::warning('Rejected Midtrans notification with an invalid payment amount.', [
                'order_id' => $paymentOrder->order_id,
            ]);

            return response()->json(['message' => 'Payment amount does not match the order.'], 422);
        } catch (Throwable $exception) {
            Log::error('Could not process Midtrans payment notification.', [
                'order_id' => $paymentOrder->order_id,
                'exception' => $exception,
            ]);

            return response()->json(['message' => 'Notification could not be processed.'], 500);
        }

        return response()->json(['message' => 'Notification processed.']);
    }
}
