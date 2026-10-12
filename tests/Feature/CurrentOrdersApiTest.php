<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Store;

class CurrentOrdersApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_orders_endpoint_returns_successful_json_response(): void
    {
        $response = $this->getJson('/api/orders/current');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'current_page',
                    'per_page',
                    'has_more_pages',
                    'next_page_url',
                    'previous_page_url',
                ],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Current orders retrieved successfully.',
            ]);
    }
    public function test_current_orders_rejects_per_page_above_maximum(): void
{
    $response = $this->getJson('/api/orders/current?per_page=500');

    $response->assertUnprocessable()
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed.',
        ])
        ->assertJsonValidationErrors(['per_page']);
}
public function test_current_orders_returns_order_with_customer_store_and_items_count(): void
{
    $customer = Customer::factory()->create();
    $store = Store::factory()->create();

    $order = Order::factory()->create([
        'customer_id' => $customer->id,
        'store_id' => $store->id,
        'driver_id' => null,
        'status' => \App\Enums\OrderStatus::Pending->value,
    ]);

    $order->items()->create([
        'item_name' => 'T-shirt',
        'unit_price' => 50.00,
        'quantity' => 2,
    ]);

    $response = $this->getJson('/api/orders/current');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.0.id', $order->id)
        ->assertJsonPath('data.0.customer.name', $customer->name)
        ->assertJsonPath('data.0.store.name', $store->name)
        ->assertJsonPath('data.0.items_count', 1);
}
}
