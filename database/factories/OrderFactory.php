<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'store_id' => Store::factory(),
            'driver_id' => null,
            'status' => OrderStatus::Pending->value,
            'delivery_address' => fake()->address(),
            'delivery_fee' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
