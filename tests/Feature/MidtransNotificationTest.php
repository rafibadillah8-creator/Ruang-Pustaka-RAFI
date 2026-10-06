<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\PaymentOrder;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MidtransGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Mockery;
use RuntimeException;
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

    public function test_wrong_order_id_does_not_grant_access(): void
    {
        $order = $this->createPaymentOrder();

        try {
            app(\App\Services\PaymentOrderFulfillment::class)->applyStatus($order, [
                'order_id' => 'DIFFERENT-' . $order->order_id,
                'transaction_status' => 'settlement',
                'gross_amount' => '100000.00',
            ]);
            $this->fail('A status for a different order must be rejected.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('Order ID Midtrans tidak cocok dengan pesanan.', $exception->getMessage());
        }

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

    public function test_admin_flag_used_on_book_page_also_grants_reader_access(): void
    {
        $order = $this->createPaymentOrder();
        $user = User::findOrFail($order->user_id);
        $user->setAttribute('is_admin', true);

        $this->actingAs($user)
            ->get('/books/' . $order->book_id . '/read')
            ->assertOk();
    }

    public function test_payment_status_lookup_failure_is_reported_instead_of_appearing_pending(): void
    {
        $order = $this->createPaymentOrder();
        $gateway = Mockery::mock(MidtransGateway::class);
        $gateway->shouldReceive('transactionStatus')
            ->once()
            ->with($order->order_id)
            ->andThrow(new RuntimeException('Transaction does not exist.'));
        $this->app->instance(MidtransGateway::class, $gateway);

        $this->actingAs(User::findOrFail($order->user_id))
            ->postJson('/books/' . $order->book_id . '/check-payment')
            ->assertStatus(503)
            ->assertJsonPath('status_unavailable', true)
            ->assertJsonPath('pending', false);
    }

    public function test_ngrok_tunnel_only_exposes_the_midtrans_notification_endpoint(): void
    {
        $this->get('https://public-test.ngrok-free.dev/')
            ->assertNotFound();

        $this->postJson('https://public-test.ngrok-free.dev/api/midtrans/notification')
            ->assertStatus(422);
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
