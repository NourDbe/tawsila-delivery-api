<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\OrderListRequest;
use App\Http\Resources\OrderResource;
use App\Models\Driver;
use App\Services\OrderQueryService;
use App\Services\OrderService;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;

final class OrderController extends Controller
{
    use HasApiResponse;

    public function __construct(
        private readonly OrderQueryService $orderQueryService,
        private readonly OrderService $orderService
    ) {
    }

    /**
     * Current active orders.
     */
    public function current(
        OrderListRequest $request
    ): JsonResponse {
        $perPage = (int) $request->input(
            'per_page',
            50
        );

        $orders = $this->orderQueryService
            ->getCurrentOrders($perPage);

        return $this->successResponse(
            OrderResource::collection(
                $orders->items()
            )->resolve($request),

            'Current orders retrieved successfully.',

            200,

            $this->paginationMeta($orders)
        );
    }

    /**
     * Today's orders, including all statuses.
     */
    public function today(
        OrderListRequest $request
    ): JsonResponse {
        $perPage = (int) $request->input(
            'per_page',
            50
        );

        $orders = $this->orderQueryService
            ->getTodayOrders($perPage);

        return $this->successResponse(
            OrderResource::collection(
                $orders->items()
            )->resolve($request),

            'Today orders retrieved successfully.',

            200,

            $this->paginationMeta($orders)
        );
    }

    /**
     * Active orders assigned to one driver.
     */
    public function currentForDriver(
        OrderListRequest $request,
        Driver $driver
    ): JsonResponse {
        $perPage = (int) $request->input(
            'per_page',
            50
        );

        $orders = $this->orderQueryService
            ->getCurrentOrdersForDriver(
                $driver,
                $perPage
            );

        return $this->successResponse(
            OrderResource::collection(
                $orders->items()
            )->resolve($request),

            'Driver current orders retrieved successfully.',

            200,

            $this->paginationMeta($orders)
        );
    }

    /**
     * Active orders waiting too long
     * without an assigned driver.
     */
    public function delayedUnassigned(
        OrderListRequest $request
    ): JsonResponse {
        $minutes = (int) $request->input(
            'minutes',
            30
        );

        $perPage = (int) $request->input(
            'per_page',
            50
        );

        $orders = $this->orderQueryService
            ->getDelayedUnassignedOrders(
                $minutes,
                $perPage
            );

        return $this->successResponse(
            OrderResource::collection(
                $orders->items()
            )->resolve($request),

            'Delayed unassigned orders retrieved successfully.',

            200,

            $this->paginationMeta($orders)
        );
    }

    /**
     * Create a new order.
     */
    public function store(
        CreateOrderRequest $request
    ): JsonResponse {
        $order = $this->orderService->create(
            $request->validated()
        );

        return $this->successResponse(
            (new OrderResource($order))
                ->resolve($request),

            'Order created successfully.',

            201
        );
    }

    /**
     * Build pagination metadata
     * for simple paginated results.
     */
    private function paginationMeta(
        $paginator
    ): array {
        return [
            'current_page' =>
                $paginator->currentPage(),

            'per_page' =>
                $paginator->perPage(),

            'has_more_pages' =>
                $paginator->hasMorePages(),

            'next_page_url' =>
                $paginator->nextPageUrl(),

            'previous_page_url' =>
                $paginator->previousPageUrl(),
        ];
    }
}
