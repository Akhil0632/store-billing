<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id'     => Customer::factory(),
            'subtotal'        => 100,
            'tax_total'       => 18,
            'grand_total'     => 118,
            'amount_given'    => 200,
            'change_returned' => 82,
        ];
    }
}
