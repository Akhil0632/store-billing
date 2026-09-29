<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_returns_the_order_history_for_a_customer_by_email(): void
    {
        $customer = Customer::factory()->create(['email' => 'thomas@example.com']);
        Order::factory()->count(3)->create(['customer_id' => $customer->id]);

        $this->getJson('/api/orders/history?email=thomas@example.com')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function test_it_returns_404_when_the_customer_email_does_not_exist(): void
    {
        $this->getJson('/api/orders/history?email=ghost@example.com')
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function test_it_returns_422_for_a_malformed_email(): void
    {
        $this->getJson('/api/orders/history?email=bad-email')
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function test_it_filters_history_by_date_range(): void
    {
        $customer = Customer::factory()->create(['email' => 'daterange@example.com']);

        Order::factory()->create([
            'customer_id' => $customer->id,
            'created_at'  => now()->subDays(30),
        ]);
        Order::factory()->create([
            'customer_id' => $customer->id,
            'created_at'  => now()->subDays(2),
        ]);

        $this->getJson(
            '/api/orders/history?email=daterange@example.com&from=' . now()->subDays(7)->toDateString()
        )
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    }

    /** @test */
    public function test_it_paginates_with_a_custom_per_page(): void
    {
        $customer = Customer::factory()->create(['email' => 'paged@example.com']);
        Order::factory()->count(25)->create(['customer_id' => $customer->id]);

        $this->getJson('/api/orders/history?email=paged@example.com&per_page=10')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 25)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonCount(10, 'data');
    }
}
