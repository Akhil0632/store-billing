<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\OrderService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderService::class);
    }

    /** @test */
    public function test_it_creates_an_order_with_correct_totals(): void
    {
        $product = Product::factory()->create([
            'price'          => 50,
            'tax_percentage' => 5,
            'stock'          => 20,
        ]);

        $order = $this->service->createOrder([
            'customer_email' => 'thomas@example.com',
            'customer_name'  => 'Thomas',
            'amount_given'   => 500,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertTrue($order->exists);

        $this->assertEquals(100.0, (float) $order->subtotal);   // 50 * 2
        $this->assertEquals(5.0,   (float) $order->tax_total);  // 100 * 0.05
        $this->assertEquals(105.0, (float) $order->grand_total);
        $this->assertEquals(500.0, (float) $order->amount_given);
        $this->assertEquals(395.0, (float) $order->change_returned);

        // Line item persisted correctly
        $this->assertCount(1, $order->items);
        $this->assertEquals(50.0,  (float) $order->items->first()->unit_price);
        $this->assertEquals(105.0, (float) $order->items->first()->line_total);
        $this->assertEquals(2, $order->items->first()->quantity);

        // Stock decremented
        $this->assertEquals(18, $product->fresh()->stock);
    }

    /** @test */
    public function test_it_aggregates_tax_across_multiple_products_with_different_rates(): void
    {
        $a = Product::factory()->create(['price' => 100, 'tax_percentage' => 5,  'stock' => 50]);
        $b = Product::factory()->create(['price' => 50,  'tax_percentage' => 18, 'stock' => 50]);

        $order = $this->service->createOrder([
            'customer_email' => 'buyer@example.com',
            'customer_name'  => 'Buyer',
            'amount_given'   => 1000,
            'items' => [
                ['product_id' => $a->id, 'quantity' => 1], // 100 + 5 tax
                ['product_id' => $b->id, 'quantity' => 2], // 100 + 18 tax
            ],
        ]);

        $this->assertEquals(200.0, (float) $order->subtotal);
        $this->assertEquals(23.0,  (float) $order->tax_total);      // 5 + 18
        $this->assertEquals(223.0, (float) $order->grand_total);
    }

    /** @test */
    public function test_it_reuses_an_existing_customer_when_the_email_already_exists(): void
    {
        $existing = Customer::factory()->create([
            'name'  => 'Original Name',
            'email' => 'repeat@example.com',
        ]);

        $product = Product::factory()->create(['price' => 10, 'tax_percentage' => 0, 'stock' => 5]);

        $this->service->createOrder([
            'customer_email' => 'repeat@example.com',
            'customer_name'  => 'Different Name', // ignored since email exists
            'amount_given'   => 100,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $this->assertSame(1, Customer::count());
        $this->assertSame($existing->id, Customer::first()->id);
        $this->assertSame('Original Name', Customer::first()->name);
    }

    /** @test */
    public function test_it_creates_a_new_customer_when_the_email_is_unknown(): void
    {
        $product = Product::factory()->create(['price' => 10, 'stock' => 5]);

        $this->service->createOrder([
            'customer_email' => 'brand-new@example.com',
            'customer_name'  => 'Newbie',
            'amount_given'   => 100,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $this->assertDatabaseHas('customers', ['email' => 'brand-new@example.com']);
    }

    // ==================================================
    // EDGE CASES
    // ==================================================

    /** @test */
    public function test_it_throws_when_stock_is_insufficient_and_does_not_touch_stock(): void
    {
        $product = Product::factory()->create([
            'stock'          => 2,
            'price'          => 10,
            'tax_percentage' => 0,
        ]);

        try {
            $this->service->createOrder([
                'customer_email' => 'thomas@example.com',
                'customer_name'  => 'Thomas',
                'amount_given'   => 500,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 5], // wants 5, only 2 available
                ],
            ]);
            $this->fail('Expected an Exception for insufficient stock.');
        } catch (Exception $e) {
            $this->assertStringContainsString('Insufficient stock', $e->getMessage());
        }

        // Nothing persisted
        $this->assertSame(0, Order::count());
        $this->assertSame(0, OrderItem::count());
        // Stock untouched
        $this->assertSame(2, $product->fresh()->stock);
    }

    /** @test */
    public function test_it_rolls_back_all_changes_when_one_item_fails_stock_check(): void
    {
        $inStock    = Product::factory()->create(['stock' => 10, 'price' => 50, 'tax_percentage' => 0]);
        $outOfStock = Product::factory()->create(['stock' => 1,  'price' => 30, 'tax_percentage' => 0]);

        try {
            $this->service->createOrder([
                'customer_email' => 'thomas@example.com',
                'customer_name'  => 'Thomas',
                'amount_given'   => 500,
                'items' => [
                    ['product_id' => $inStock->id,    'quantity' => 2], // valid
                    ['product_id' => $outOfStock->id, 'quantity' => 5], // fails
                ],
            ]);
            $this->fail('Expected an Exception for insufficient stock.');
        } catch (Exception $e) {
            $this->assertStringContainsString('Insufficient stock', $e->getMessage());
        }

        // The first item's stock decrement must have been rolled back
        $this->assertSame(10, $inStock->fresh()->stock);
        $this->assertSame(1,  $outOfStock->fresh()->stock);
        $this->assertSame(0,  Order::count());
        $this->assertSame(0,  OrderItem::count());
    }

    /** @test */
    public function test_it_throws_when_amount_given_is_less_than_grand_total(): void
    {
        $product = Product::factory()->create([
            'price'          => 100,
            'tax_percentage' => 0,
            'stock'          => 10,
        ]);

        try {
            $this->service->createOrder([
                'customer_email' => 'thomas@example.com',
                'customer_name'  => 'Thomas',
                'amount_given'   => 50, // less than 100
                'items' => [['product_id' => $product->id, 'quantity' => 1]],
            ]);
            $this->fail('Expected an Exception for insufficient payment.');
        } catch (Exception $e) {
            $this->assertStringContainsString('less than the grand total', $e->getMessage());
        }

        $this->assertSame(0, Order::count());
        $this->assertSame(10, $product->fresh()->stock); // stock not deducted
    }

    /** @test */
    public function test_it_accepts_exact_payment_with_zero_change(): void
    {
        $product = Product::factory()->create([
            'price'          => 100,
            'tax_percentage' => 0,
            'stock'          => 5,
        ]);

        $order = $this->service->createOrder([
            'customer_email' => 'exact@example.com',
            'customer_name'  => 'Exact',
            'amount_given'   => 100,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $this->assertEquals(0.0, (float) $order->change_returned);
    }

    /** @test */
    public function test_it_handles_a_zero_tax_product(): void
    {
        $product = Product::factory()->noTax()->create(['price' => 40, 'stock' => 10]);

        $order = $this->service->createOrder([
            'customer_email' => 'notax@example.com',
            'customer_name'  => 'No Tax',
            'amount_given'   => 100,
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);

        $this->assertEquals(80.0, (float) $order->subtotal);
        $this->assertEquals(0.0,  (float) $order->tax_total);
        $this->assertEquals(80.0, (float) $order->grand_total);
    }

    /** @test */
    public function test_it_handles_large_quantities_without_numeric_overflow(): void
    {
        $product = Product::factory()->create([
            'price'          => 999.99,
            'tax_percentage' => 18,
            'stock'          => 10_000,
        ]);

        $order = $this->service->createOrder([
            'customer_email' => 'bulk@example.com',
            'customer_name'  => 'Bulk',
            'amount_given'   => 10_000_000,
            'items' => [['product_id' => $product->id, 'quantity' => 5_000]],
        ]);

        $this->assertEquals(4_999_950.0, (float) $order->subtotal);
        $this->assertEquals(5_899_941.0, (float) $order->grand_total);
    }
}
