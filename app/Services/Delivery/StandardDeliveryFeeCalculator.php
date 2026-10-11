<?php

declare(strict_types=1);

namespace App\Services\Delivery;

use App\Contracts\DeliveryFeeCalculatorInterface;

final class StandardDeliveryFeeCalculator implements
    DeliveryFeeCalculatorInterface
{
    /**
     * Calculate the standard delivery fee.
     *
     * The actual amount comes from configuration
     * instead of being hard-coded inside services.
     */
    public function calculate(): string
    {
        return (string) config(
            'delivery.standard_fee',
            '25.00'
        );
    }
}
