<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'           => $this->faker->unique()->words(2, true),
            'code'           => strtoupper($this->faker->unique()->bothify('??###')),
            'price'          => $this->faker->randomFloat(2, 10, 200),
            'tax_percentage' => $this->faker->randomElement([0, 5, 12, 18]),
            'stock'          => $this->faker->numberBetween(1, 100),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function withStock(int $qty): static
    {
        return $this->state(fn () => ['stock' => $qty]);
    }

    public function noTax(): static
    {
        return $this->state(fn () => ['tax_percentage' => 0]);
    }
}
