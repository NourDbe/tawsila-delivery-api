<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\ExcludeSuspendedStoresScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_suspended',
    ];

    /**
     * Cast database values to useful PHP types.
     */
    protected function casts(): array
    {
        return [
            'is_suspended' => 'boolean',
        ];
    }

    /**
     * Register model-level behavior.
     *
     * Suspended stores are hidden automatically
     * from normal Store queries.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            new ExcludeSuspendedStoresScope()
        );
    }

    /**
     * Orders belonging to this store.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
