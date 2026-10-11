<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Driver;
use App\Models\Order;
use App\Models\Scopes\ExcludeSuspendedStoresScope;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

final class OrderQueryService
{
    /**
     * Get current active orders.
     *
     * The query is optimized for list endpoints:
     *
     * - Uses Local Scopes.
     * - Selects only required columns.
     * - Eager loads required relationships.
     * - Uses withCount() instead of loading all items.
     * - Uses pagination instead of loading all orders.
     */
    public function getCurrentOrders(
        int $perPage = 50
    ): Paginator {
        return $this->baseListQuery()
            ->active()
            ->oldestFirst()
            ->simplePaginate($perPage);
    }

    /**
     * Get active orders assigned
     * to one specific driver.
     */
    public function getCurrentOrdersForDriver(
        Driver $driver,
        int $perPage = 50
    ): Paginator {
        return $this->baseListQuery()
            ->active()
            ->forDriver($driver->id)
            ->oldestFirst()
            ->simplePaginate($perPage);
    }

    /**
     * Get active orders that:
     *
     * - Have no assigned driver.
     * - Are older than the given number of minutes.
     */
    public function getDelayedUnassignedOrders(
        int $minutes = 30,
        int $perPage = 50
    ): Paginator {
        return $this->baseListQuery()
            ->active()
            ->unassigned()
            ->olderThanMinutes($minutes)
            ->oldestFirst()
            ->simplePaginate($perPage);
    }

    /**
     * Get all orders created today.
     *
     * Notice that this method does NOT call active().
     *
     * A daily report may need:
     * Pending, Accepted, PickedUp,
     * Delivered and Cancelled orders.
     */
    public function getTodayOrders(
        int $perPage = 50
    ): Paginator {
        return $this->baseListQuery()
            ->today()
            ->oldestFirst()
            ->simplePaginate($perPage);
    }

    /**
     * Build the optimized base query
     * shared by order list endpoints.
     *
     * Query conditions stay inside Local Scopes.
     * This method only centralizes the shape
     * of the list query.
     */
    private function baseListQuery(): Builder
    {
        return Order::query()

            /*
             * Select only fields actually required
             * by the list response.
             *
             * Foreign keys must remain selected
             * because Eloquent needs them
             * for eager-loaded relationships.
             */
            ->select([
                'id',
                'customer_id',
                'store_id',
                'driver_id',
                'status',
                'created_at',
            ])

            /*
             * Eager loading prevents N+1.
             */
            ->with([
                /*
                 * The list only needs
                 * customer ID and name.
                 */
                'customer' => function ($query) {
                    $query->select([
                        'id',
                        'name',
                    ]);
                },

                /*
                 * A store may have been suspended
                 * after the order was created.
                 *
                 * Staff still need to see the store
                 * attached to that order.
                 *
                 * Therefore we intentionally bypass
                 * the Store Global Scope here.
                 */
                'store' => function ($query) {
                    $query
                        ->withoutGlobalScope(
                            ExcludeSuspendedStoresScope::class
                        )
                        ->select([
                            'id',
                            'name',
                        ]);
                },

                /*
                 * A driver may be null
                 * for an unassigned order.
                 */
                'driver' => function ($query) {
                    $query->select([
                        'id',
                        'name',
                    ]);
                },
            ])

            /*
             * We only need the number of order items.
             *
             * Do not eager-load every item
             * just to count them.
             */
            ->withCount('items');
    }
}
