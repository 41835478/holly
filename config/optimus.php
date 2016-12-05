<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Optimus Connection
    |--------------------------------------------------------------------------
    |
    | More info: https://github.com/jenssegers/optimus#usage
    |
    | - Large prime number lower than 2147483647
    | - The inverse prime so that (PRIME * INVERSE) & MAXID == 1
    | - A large random integer lower than 2147483647
    |
    | You may generate them via `php artisan optimus:generate`.
    |
    */

    'prime' => env('OPTIMUS_PRIME'),
    'inverse' => env('OPTIMUS_INVERSE'),
    'random' => env('OPTIMUS_RANDOM'),
];
