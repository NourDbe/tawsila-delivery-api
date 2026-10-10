<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case PickedUp = 'picked_up';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    /**
     * Statuses that represent an active order.
     *
     * An active order has not been delivered
     * and has not been cancelled.
     *
     * @return array<int, string>
     */
    public static function activeValues(): array
    {
        return [
            self::Pending->value,
            self::Accepted->value,
            self::PickedUp->value,
        ];
    }

    /**
     * Determine whether this status
     * represents a finished order.
     */
    public function isTerminal(): bool
    {
        return match ($this) {
            self::Delivered,
            self::Cancelled => true,

            default => false,
        };
    }
}
