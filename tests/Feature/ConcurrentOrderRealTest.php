<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConcurrentOrderRealTest extends TestCase
{
    use RefreshDatabase;

     protected function setUp(): void
    {
        parent::setUp();

        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped(
                'Real concurrency requires MySQL/InnoDB. SQLite uses per-connection '
                . ':memory: databases, so parallel requests cannot share state. '
                . 'Run with DB_CONNECTION=mysql to execute this test.'
            );
        }
    }

    /**
     * @group concurrency
     * @group slow
     */
    public function test_real_concurrent_orders_do_not_oversell(): void
    {
        $product = Product::factory()->create([
            'stock'          => 1,
            'price'     => 100,
            'tax_percentage' => 0,
        ]);

        $productId = $product->id;
        $payload = json_encode([
            'customer_email' => 'concurrent@example.com',
            'customer_name'  => 'Concurrent',
            'amount_given'   => 500,
            'items' => [
                ['product_id' => $productId, 'quantity' => 1],
            ],
        ]);

        // Fire 5 parallel curl requests to the API
        $mh = curl_multi_init();
        $handles = [];

        for ($i = 0; $i < 5; $i++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => config('app.url') . '/api/orders',
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
        }

        // Execute all in parallel
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        // Collect status codes
        $statuses = [];
        foreach ($handles as $ch) {
            $statuses[] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);

        // Count outcomes
        $created = count(array_filter($statuses, fn ($s) => $s === 201));
        $failed  = count(array_filter($statuses, fn ($s) => $s === 422));

        $this->assertSame(1, $created, 'Exactly one request should create an order');
        $this->assertSame(4, $failed,  'Exactly four requests should fail with 422');
        $this->assertSame(0, $product->fresh()->stock, 'Stock must be 0, never negative');
        $this->assertSame(1, \App\Models\Order::count(), 'Only one order should exist');
    }
}
