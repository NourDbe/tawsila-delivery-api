<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'store_id',
        'driver_id',
        'status',
        'delivery_address',
        'delivery_fee',
    ];

    /**
     * Cast database values to useful PHP types.
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'delivery_fee' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Customer who created the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Store preparing the order.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Driver assigned to the order.
     *
     * The relation may be null before
     * a driver is assigned.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Items included in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Get only active orders.
     *
     * Active orders are:
     * - Pending
     * - Accepted
     * - Picked Up
     *
     * Delivered and Cancelled orders
     * are no longer considered active.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn(
            'status',
            OrderStatus::activeValues()
        );
    }

    /**
     * Filter orders assigned to
     * a specific driver.
     */
    public function scopeForDriver(
        Builder $query,
        int $driverId
    ): Builder {
        return $query->where(
            'driver_id',
            $driverId
        );
    }

    /**
     * Get orders that do not have
     * an assigned driver yet.
     */
    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('driver_id');
    }

    /**
     * Get orders older than
     * the provided number of minutes.
     *
     * Example:
     *
     * Order::olderThanMinutes(30)
     */
    public function scopeOlderThanMinutes(
        Builder $query,
        int $minutes
    ): Builder {
        return $query->where(
            'created_at',
            '<=',
            now()->subMinutes($minutes)
        );
    }

    /**
     * Get orders created today.
     *
     * We use a date range instead of whereDate()
     * so the database can make better use
     * of the created_at index.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereBetween(
            'created_at',
            [
                now()->startOfDay(),
                now()->endOfDay(),
            ]
        );
    }

    /**
     * Sort orders from oldest
     * to newest.
     */
    public function scopeOldestFirst(Builder $query): Builder
    {
        return $query->orderBy(
            'created_at',
            'asc'
        );
    }
}
