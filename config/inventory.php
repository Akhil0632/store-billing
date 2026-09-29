<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | Products with a stock quantity strictly below this value will be
    | considered "low stock". Override via .env with LOW_STOCK_THRESHOLD.
    |
    */
    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 10),
];
