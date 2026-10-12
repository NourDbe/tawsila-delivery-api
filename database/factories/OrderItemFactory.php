<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'item_name' => fake()->words(2, true),
            'unit_price' => fake()->randomFloat(2, 1, 500),
            'quantity' => fake()->numberBetween(1, 10),
        ];
    }
}
