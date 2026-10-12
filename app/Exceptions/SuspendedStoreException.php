<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class SuspendedStoreException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Orders cannot be created from a suspended store.'
        );
    }
}
