<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Scopes\ExcludeSuspendedStoresScope;
use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;

final class StoreService
{
    /**
     * Get stores visible to customers.
     *
     * The global scope automatically excludes
     * suspended stores.
     */
    public function getVisibleStores(): Collection
    {
        return Store::query()
            ->select([
                'id',
                'name',
                'address',
            ])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all stores for administration reports.
     *
     * The Global Scope is intentionally disabled
     * because administrators must be able to see
     * suspended stores as well.
     */
    public function getAllStoresForAdmin(): Collection
    {
        return Store::query()
            ->withoutGlobalScope(
                ExcludeSuspendedStoresScope::class
            )
            ->select([
                'id',
                'name',
                'address',
                'is_suspended',
            ])
            ->orderBy('name')
            ->get();
    }
}
