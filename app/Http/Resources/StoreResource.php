<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,

            /*
             * Customer-facing queries do not select
             * is_suspended.
             *
             * Admin queries do.
             */
            'is_suspended' => $this->when(
                $this->getAttribute('is_suspended') !== null,
                $this->is_suspended
            ),
        ];
    }
}
