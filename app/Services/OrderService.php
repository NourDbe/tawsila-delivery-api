<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DeliveryFeeCalculatorInterface;
use App\Enums\OrderStatus;
use App\Exceptions\StoreNotFoundException;
use App\Exceptions\SuspendedStoreException;
use App\Models\Order;
use App\Models\Scopes\ExcludeSuspendedStoresScope;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

final class OrderService
{
    public function __construct(
        private readonly DeliveryFeeCalculatorInterface
            $deliveryFeeCalculator
    ) {
    }

    /**
     * Create a complete order.
     *
     * Order and its items must either
     * be saved together or not saved at all.
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data): Order {

            /*
             * Search including suspended stores
             * so we can distinguish:
             *
             * - Store does not exist.
             * - Store exists but is suspended.
             */
            $store = Store::query()
                ->withoutGlobalScope(
                    ExcludeSuspendedStoresScope::class
                )
                ->find($data['store_id']);

            if ($store === null) {
                throw new StoreNotFoundException();
            }

            if ($store->is_suspended) {
                throw new SuspendedStoreException();
            }

            /*
             * Delivery fee is calculated through
             * the Service Container dependency.
             */
            $deliveryFee = $this
                ->deliveryFeeCalculator
                ->calculate();

            /*
             * Every new order starts Pending
             * and without an assigned driver.
             */
            $order = Order::query()->create([
                'customer_id' => $data['customer_id'],
                'store_id' => $store->id,
                'driver_id' => null,
                'status' => OrderStatus::Pending,
                'delivery_address' => $data['delivery_address'],
                'delivery_fee' => $deliveryFee,
            ]);

            /*
             * Create all order items.
             */
            $order->items()->createMany(
                $data['items']
            );

            /*
             * Load only relations required
             * for the API response.
             */
            return $order
                ->load([
                    'customer:id,name',
                    'store:id,name',
                ])
                ->loadCount('items');
        });
    }

    /**
     * Calculate the current delivery fee.
     */
    public function calculateDeliveryFee(): string
    {
        return $this->deliveryFeeCalculator
            ->calculate();
    }
}
