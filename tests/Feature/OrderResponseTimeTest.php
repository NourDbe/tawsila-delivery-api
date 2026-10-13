<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderResponseTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_orders_endpoint_records_response_duration(): void
    {
        $customer = Customer::factory()->create();
        $store = Store::factory()->create();

        Order::factory()->count(15)->create([
            'customer_id' => $customer->id,
            'store_id' => $store->id,
            'driver_id' => null,
        ]);

        $durations = [];

        // نرسل 6 طلبات؛ الأول للتهيئة، والخمسة الباقية للقياس.
        for ($i = 0; $i < 6; $i++) {
            $startTime = hrtime(true);

            $response = $this->getJson('/api/orders/current');

            $durationInMilliseconds =
                (hrtime(true) - $startTime) / 1_000_000;

            $response->assertOk();

            if ($i > 0) {
                $durations[] = $durationInMilliseconds;
            }
        }

        sort($durations);

        $median = $durations[2];

        $this->assertCount(5, $durations);
        $this->assertGreaterThanOrEqual(0, $median);

        fwrite(
            STDERR,
            sprintf(
                "\n/api/orders/current median: %.2f ms\n",
                $median
            )
        );
    }
}
