<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Services\StoreService;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StoreController extends Controller
{
    use HasApiResponse;

    public function __construct(
        private readonly StoreService $storeService
    ) {
    }

    /**
     * Customer-facing store list.
     *
     * Suspended stores are automatically hidden
     * by the Global Scope.
     */
    public function index(Request $request): JsonResponse
    {
        $stores = $this->storeService
            ->getVisibleStores();

        return $this->successResponse(
            StoreResource::collection($stores)->resolve($request),
            'Stores retrieved successfully.'
        );
    }

    /**
     * Administrative store list.
     *
     * Includes suspended stores intentionally.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $stores = $this->storeService
            ->getAllStoresForAdmin();

        return $this->successResponse(
            StoreResource::collection($stores)->resolve($request),
            'Admin stores retrieved successfully.'
        );
    }
}
