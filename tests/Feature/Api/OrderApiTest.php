<?php

namespace Tests\Feature\Api;

use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Mail::fake();
    }

    /** @test */
    public function test_it_creates_an_order_and_returns_201_with_the_order_resource(): void
    {
        $product = Product::factory()->create([
            'price'          => 50,
            'tax_percentage' => 5,
            'stock'          => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer_email' => 'thomas@example.com',
            'customer_name'  => 'Thomas',
            'amount_given'   => 500,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.customer.email', 'thomas@example.com')
            ->assertJsonPath('data.subtotal', 100)
            ->assertJsonPath('data.tax_total', 5)
            ->assertJsonPath('data.grand_total', 105)
            ->assertJsonPath('data.change_returned', 395)
            ->assertJsonPath('data.items.0.unit_price', 50)
            ->assertJsonPath('data.items.0.line_total', 105);

        $this->assertSame(1, Order::count());
        $this->assertSame(8, $product->fresh()->stock);
    }

    /** @test */
    public function test_it_dispatches_the_order_confirmation_job_on_success(): void
    {
        $product = Product::factory()->create(['price' => 10, 'tax_percentage' => 0, 'stock' => 5]);

        $this->postJson('/api/orders', [
            'customer_email' => 'queue@example.com',
            'customer_name'  => 'Queued',
            'amount_given'   => 100,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertCreated();

        Queue::assertPushed(SendOrderConfirmationEmail::class, function ($job) {
            return $job->order->customer->email === 'queue@example.com';
        });
    }

    /** @test */
    public function test_it_does_not_dispatch_the_job_when_order_creation_fails(): void
    {
        $product = Product::factory()->create(['stock' => 1]);

        $this->postJson('/api/orders', [
            'customer_email' => 'fail@example.com',
            'customer_name'  => 'Fail',
            'amount_given'   => 500,
            'items' => [['product_id' => $product->id, 'quantity' => 99]],
        ])->assertStatus(422);

        Queue::assertNotPushed(SendOrderConfirmationEmail::class);
    }

    // ==================================================
    // VALIDATION
    // ==================================================

    /** @test */
    public function test_it_rejects_an_invalid_email(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->postJson('/api/orders', [
            'customer_email' => 'not-an-email',
            'customer_name'  => 'X',
            'amount_given'   => 500,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertStatus(422)
          ->assertJsonValidationErrors('customer_email');
    }

    /** @test */
    public function test_it_rejects_an_order_with_no_items(): void
    {
        $this->postJson('/api/orders', [
            'customer_email' => 'thomas@example.com',
            'customer_name'  => 'Thomas',
            'amount_given'   => 500,
            'items' => [],
        ])->assertStatus(422)
          ->assertJsonValidationErrors('items');
    }

    /** @test */
    public function test_it_rejects_a_nonexistent_product(): void
    {
        $this->postJson('/api/orders', [
            'customer_email' => 'thomas@example.com',
            'customer_name'  => 'Thomas',
            'amount_given'   => 500,
            'items' => [['product_id' => 99999, 'quantity' => 1]],
        ])->assertStatus(422)
          ->assertJsonValidationErrors('items.0.product_id');
    }

    /** @test */
    public function test_it_rejects_a_zero_or_negative_quantity(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->postJson('/api/orders', [
            'customer_email' => 'thomas@example.com',
            'customer_name'  => 'Thomas',
            'amount_given'   => 500,
            'items' => [['product_id' => $product->id, 'quantity' => 0]],
        ])->assertStatus(422)
          ->assertJsonValidationErrors('items.0.quantity');
    }

    // ==================================================
    // EDGE CASES (API-level)
    // ==================================================

    /** @test */
    public function test_it_returns_422_when_stock_is_insufficient(): void
    {
        $product = Product::factory()->create([
            'stock'          => 2,
            'price'          => 10,
            'tax_percentage' => 0,
        ]);

        $this->postJson('/api/orders', [
            'customer_email' => 'thomas@example.com',
            'customer_name'  => 'Thomas',
            'amount_given'   => 500,
            'items' => [['product_id' => $product->id, 'quantity' => 5]],
        ])->assertStatus(422)
          ->assertJsonPath('success', false)
          ->assertJsonFragment([
              'message' => "Insufficient stock for '{$product->name}'. Available: 2, Requested: 5.",
          ]);

        $this->assertSame(0, Order::count());
        $this->assertSame(2, $product->fresh()->stock);
    }

    /** @test */
    public function test_it_returns_422_when_amount_given_is_less_than_grand_total(): void
    {
        $product = Product::factory()->create([
            'price'          => 100,
            'tax_percentage' => 0,
            'stock'          => 5,
        ]);

        $this->postJson('/api/orders', [
            'customer_email' => 'thomas@example.com',
            'customer_name'  => 'Thomas',
            'amount_given'   => 50,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertStatus(422)
          ->assertJsonPath('success', false);

        $this->assertSame(0, Order::count());
    }
}
