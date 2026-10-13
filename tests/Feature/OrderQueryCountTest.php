<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderQueryCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_orders_query_count_does_not_grow_with_order_count(): void
    {
        $customer = Customer::factory()->create();
        $store = Store::factory()->create();

        Order::factory()->count(5)->create([
            'customer_id' => $customer->id,
            'store_id' => $store->id,
            'driver_id' => null,
        ]);

        $queryCountForFiveOrders = $this->countQueriesForCurrentOrders();

        Order::factory()->count(10)->create([
            'customer_id' => $customer->id,
            'store_id' => $store->id,
            'driver_id' => null,
        ]);

        $queryCountForFifteenOrders = $this->countQueriesForCurrentOrders();


        $this->assertSame(
            $queryCountForFiveOrders,
            $queryCountForFifteenOrders,
            sprintf(
                'Expected a constant query count, but got %d queries for 5 orders and %d for 15 orders.',
                $queryCountForFiveOrders,
                $queryCountForFifteenOrders,
            ),
        );
    }

    private function countQueriesForCurrentOrders(): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $this->getJson('/api/orders/current')->assertOk();

            return count(DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }
    }
}
