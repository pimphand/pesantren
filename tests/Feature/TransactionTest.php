<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $merchant;
    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test merchant
        $this->merchant = Merchant::factory()->create([
            'is_pin' => false,
            'is_tax' => false,
        ]);

        // Create test user
        $this->user = User::factory()->create([
            'balance' => 1000000,
        ]);

        // Create test product
        $this->product = Product::factory()->create([
            'merchant_id' => $this->merchant->id,
            'stock' => 10,
            'price' => 10000,
        ]);

        // Login as merchant
        $this->actingAs($this->merchant->user);
    }

    /** @test */
    public function it_prevents_double_transaction_with_same_token()
    {
        // Generate transaction token
        $tokenResponse = $this->get(route('merchant.transaction.token'));
        $tokenData = $tokenResponse->json();
        $token = $tokenData['token'];

        // Prepare transaction data
        $transactionData = [
            'user_id' => $this->user->uuid,
            'items' => [
                [
                    'product' => $this->product->id,
                    'qty' => 2
                ]
            ]
        ];

        // First transaction attempt (should succeed)
        $response1 = $this->withHeaders([
            'X-Transaction-Token' => $token
        ])->post(route('merchant.transactions.store'), $transactionData);

        $response1->assertStatus(200);
        $this->assertEquals('Transaksi berhasil', $response1->json()['message']);

        // Verify product stock was reduced
        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock);

        // Verify user balance was reduced
        $this->user->refresh();
        $this->assertEquals(980000, $this->user->balance);

        // Second transaction attempt with same token (should fail)
        $response2 = $this->withHeaders([
            'X-Transaction-Token' => $token
        ])->post(route('merchant.transactions.store'), $transactionData);

        $response2->assertStatus(409);
        $this->assertEquals('Transaction has already been processed', $response2->json()['message']);

        // Verify product stock was not reduced again
        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock);

        // Verify user balance was not reduced again
        $this->user->refresh();
        $this->assertEquals(980000, $this->user->balance);
    }

    /** @test */
    public function it_allows_new_transaction_with_new_token()
    {
        // First transaction
        $token1 = $this->get(route('merchant.transaction.token'))->json()['token'];

        $transactionData = [
            'user_id' => $this->user->uuid,
            'items' => [
                [
                    'product' => $this->product->id,
                    'qty' => 2
                ]
            ]
        ];

        $response1 = $this->withHeaders([
            'X-Transaction-Token' => $token1
        ])->post(route('merchant.transactions.store'), $transactionData);

        $response1->assertStatus(200);

        // Second transaction with new token
        $token2 = $this->get(route('merchant.transaction.token'))->json()['token'];

        $response2 = $this->withHeaders([
            'X-Transaction-Token' => $token2
        ])->post(route('merchant.transactions.store'), $transactionData);

        $response2->assertStatus(200);

        // Verify product stock was reduced twice
        $this->product->refresh();
        $this->assertEquals(6, $this->product->stock);

        // Verify user balance was reduced twice
        $this->user->refresh();
        $this->assertEquals(960000, $this->user->balance);
    }

    /** @test */
    public function it_requires_transaction_token()
    {
        $transactionData = [
            'user_id' => $this->user->uuid,
            'items' => [
                [
                    'product' => $this->product->id,
                    'qty' => 2
                ]
            ]
        ];

        $response = $this->post(route('merchant.transactions.store'), $transactionData);

        $response->assertStatus(422);
        $this->assertEquals('Transaction token is required', $response->json()['message']);
    }

    /** @test */
    public function it_handles_idempotent_transactions()
    {
        $idempotencyKey = 'test-key-' . time();

        $transactionData = [
            'user_id' => $this->user->uuid,
            'items' => [
                [
                    'product' => $this->product->id,
                    'qty' => 2
                ]
            ]
        ];

        // First request
        $response1 = $this->withHeaders([
            'Idempotency-Key' => $idempotencyKey
        ])->post(route('merchant.transactions.store'), $transactionData);

        $response1->assertStatus(200);
        $firstResponseData = $response1->json();

        // Verify product stock was reduced
        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock);

        // Verify user balance was reduced
        $this->user->refresh();
        $this->assertEquals(980000, $this->user->balance);

        // Second request with same idempotency key
        $response2 = $this->withHeaders([
            'Idempotency-Key' => $idempotencyKey
        ])->post(route('merchant.transactions.store'), $transactionData);

        $response2->assertStatus(200);
        $secondResponseData = $response2->json();

        // Verify responses are identical
        $this->assertEquals($firstResponseData, $secondResponseData);

        // Verify product stock was not reduced again
        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock);

        // Verify user balance was not reduced again
        $this->user->refresh();
        $this->assertEquals(980000, $this->user->balance);

        // Verify idempotency key was stored
        $this->assertDatabaseHas('idempotency_keys', [
            'key' => $idempotencyKey,
            'merchant_id' => $this->merchant->id,
            'status' => 'success'
        ]);
    }

    /** @test */
    public function it_requires_idempotency_key()
    {
        $transactionData = [
            'user_id' => $this->user->uuid,
            'items' => [
                [
                    'product' => $this->product->id,
                    'qty' => 2
                ]
            ]
        ];

        $response = $this->post(route('merchant.transactions.store'), $transactionData);

        $response->assertStatus(422);
        $this->assertEquals('Idempotency-Key header is required', $response->json()['message']);
    }
}
