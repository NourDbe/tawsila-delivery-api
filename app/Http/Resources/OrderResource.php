<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the order into an API response.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'status' => $this->status?->value,

            /*
             * This field exists when withCount('items')
             * was used by the query.
             */
            'items_count' => $this->whenCounted('items'),

            /*
             * delivery_fee may not be selected
             * by every list query.
             */
            'delivery_fee' => $this->when(
                $this->getAttribute('delivery_fee') !== null,
                $this->delivery_fee
            ),

            'customer' => $this->whenLoaded(
                'customer',
                function () {
                    return [
                        'id' => $this->customer->id,
                        'name' => $this->customer->name,
                    ];
                }
            ),

            'store' => $this->whenLoaded(
                'store',
                function () {
                    return [
                        'id' => $this->store->id,
                        'name' => $this->store->name,
                    ];
                }
            ),

            'driver' => $this->whenLoaded(
                'driver',
                function () {
                    if ($this->driver === null) {
                        return null;
                    }

                    return [
                        'id' => $this->driver->id,
                        'name' => $this->driver->name,
                    ];
                }
            ),

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
