<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DeliveryFeeCalculatorInterface;

final class OrderService
{
    public function __construct(
        private readonly DeliveryFeeCalculatorInterface
            $deliveryFeeCalculator
    ) {
    }

    /**
     * Get the delivery fee that should
     * be stored when creating an order.
     */
    public function calculateDeliveryFee(): string
    {
        return $this->deliveryFeeCalculator
            ->calculate();
    }
}
