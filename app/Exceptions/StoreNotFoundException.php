<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class StoreNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'The requested store was not found.'
        );
    }
}
