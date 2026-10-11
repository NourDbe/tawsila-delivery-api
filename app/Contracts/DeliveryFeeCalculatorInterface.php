<?php

declare(strict_types=1);

namespace App\Contracts;

interface DeliveryFeeCalculatorInterface
{
    /**
     * Calculate the delivery fee for an order.
     *
     * The concrete implementation decides
     * how the fee is calculated.
     */
    public function calculate(): string;
}
