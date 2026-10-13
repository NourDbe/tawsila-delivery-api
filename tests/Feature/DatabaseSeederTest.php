<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_expected_delivery_data(): void
    {
        $this->seed();

        $this->assertSame(100, Customer::count());
        $this->assertSame(20, Store::count());
        $this->assertSame(10, Driver::count());
        $this->assertSame(2000, Order::count());

        $this->assertGreaterThanOrEqual(2000, OrderItem::count());
        $this->assertLessThanOrEqual(8000, OrderItem::count());

        $assignedOrders = Order::whereNotNull('driver_id')->count();
        $unassignedOrders = Order::whereNull('driver_id')->count();

        $this->assertGreaterThan(0, $assignedOrders);
        $this->assertGreaterThan(0, $unassignedOrders);

        $this->assertSame(
            2000,
            $assignedOrders + $unassignedOrders
        );
    }
}
