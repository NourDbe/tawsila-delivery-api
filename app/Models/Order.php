<?php

declare(strict_types=1);

namespace App\Models;

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

    protected function casts(): array
    {
        return [
            'delivery_fee' => 'decimal:2',
        ];
    }

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
}
