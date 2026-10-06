<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\PaymentOrder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransNotificationTest extends TestCase
{
    use RefreshDatabase;

    private const SERVER_KEY = 'Mid-server-test-key';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.midtrans.server_key' => self::SERVER_KEY]);
    }

    public function test_valid_settlement_notification_grants_book_access_idempotently(): void
    {
        $order = $this->createPaymentOrder();
        $payload = $this->notificationPayload($order->order_id, '100000.00');

        $this->postJson('/api/midtrans/notification', $payload)->assertOk();
        $this->postJson('/api/midtrans/notification', $payload)->assertOk();

        $this->assertDatabaseHas('payment_orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $order->user_id,
            'book_id' => $order->book_id,
            'price' => 100000,
        ]);
        $this->assertSame(1, Transaction::where('user_id', $order->user_id)
            ->where('book_id', $order->book_id)
            ->count());
        $this->actingAs(User::findOrFail($order->user_id))
            ->get('/books/' . $order->book_id . '/read')
            ->assertOk();
    }

    public function test_invalid_signature_does_not_grant_access(): void
    {
        $order = $this->createPaymentOrder();
        $payload = $this->notificationPayload($order->order_id, '100000.00');
        $payload['signature_key'] = str_repeat('0', 128);

        $this->postJson('/api/midtrans/notification', $payload)->assertForbidden();

        $this->assertDatabaseHas('payment_orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('transactions', [
            'user_id' => $order->user_id,
            'book_id' => $order->book_id,
        ]);
    }

    public function test_wrong_amount_does_not_grant_access(): void
    {
        $order = $this->createPaymentOrder();
        $payload = $this->notificationPayload($order->order_id, '1.00');

        $this->postJson('/api/midtrans/notification', $payload)->assertStatus(422);

        $this->assertDatabaseHas('payment_orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('transactions', [
            'user_id' => $order->user_id,
            'book_id' => $order->book_id,
        ]);
    }

    public function test_session_purchase_flag_does_not_grant_access(): void
    {
        $order = $this->createPaymentOrder();
        $user = User::findOrFail($order->user_id);

        $this->actingAs($user)
            ->withSession(['purchased_books_' . $order->book_id => true])
            ->get('/books/' . $order->book_id . '/read')
            ->assertRedirect('/books/' . $order->book_id);
    }

    private function createPaymentOrder(): PaymentOrder
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test']);
        $book = Book::create([
            'title' => 'Test book',
            'author' => 'Test author',
            'price' => 100000,
            'category_id' => $category->id,
            'file_path' => 'books/test.pdf',
        ]);

        return PaymentOrder::create([
            'order_id' => 'TEST-' . uniqid(),
            'user_id' => $user->id,
            'book_id' => $book->id,
            'price' => 100000,
            'status' => 'pending',
        ]);
    }

    private function notificationPayload(string $orderId, string $grossAmount): array
    {
        $payload = [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ];

        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . self::SERVER_KEY
        );

        return $payload;
    }
}
