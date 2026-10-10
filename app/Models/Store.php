<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_suspended',
    ];

    protected function casts(): array
    {
        return [
            'is_suspended' => 'boolean',
        ];
    }

    /**
     * Orders belonging to this store.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
