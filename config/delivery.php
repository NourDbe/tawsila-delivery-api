<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Standard Delivery Fee
    |--------------------------------------------------------------------------
    |
    | Default delivery fee used by the standard
    | delivery fee calculator.
    |
    */

    'standard_fee' => env(
        'STANDARD_DELIVERY_FEE',
        '25.00'
    ),

];
