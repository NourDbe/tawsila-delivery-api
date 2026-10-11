<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\DeliveryFeeCalculatorInterface;
use App\Services\Delivery\StandardDeliveryFeeCalculator;
use Tests\TestCase;

class DeliveryFeeCalculatorTest extends TestCase
{
    public function test_service_container_resolves_standard_calculator(): void
    {
        $calculator = $this->app->make(
            DeliveryFeeCalculatorInterface::class
        );

        $this->assertInstanceOf(
            StandardDeliveryFeeCalculator::class,
            $calculator
        );
    }

    public function test_standard_delivery_fee_is_calculated(): void
    {
        config([
            'delivery.standard_fee' => '25.00',
        ]);

        $calculator = $this->app->make(
            DeliveryFeeCalculatorInterface::class
        );

        $this->assertSame(
            '25.00',
            $calculator->calculate()
        );
    }
}
