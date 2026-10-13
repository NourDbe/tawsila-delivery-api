<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            // Create base data for testing.
            $customers = Customer::factory()
                ->count(100)
                ->create();

            $stores = Store::factory()
                ->count(20)
                ->create();

            $drivers = Driver::factory()
                ->count(10)
                ->create();

            // Create 2,000 orders and reuse existing customers and stores.
            $orders = Order::factory()
                ->count(2000)
                ->recycle($customers)
                ->recycle($stores)
                ->state(fn (): array => [
                    'driver_id' => fake()->boolean(70)
                        ? $drivers->random()->id
                        : null,
                ])
                ->create();

            // Create 1–4 items for each order.
            $orders->each(function (Order $order): void {
                OrderItem::factory()
                    ->count(fake()->numberBetween(1, 4))
                    ->create([
                        'order_id' => $order->id,
                    ]);
            });
        });
    }
}
