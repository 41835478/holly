<?php

namespace App\Http\Middleware;

use App\Support\Http\Middleware\VerifyApiToken as BaseVerifier;

class VerifyApiToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from API Token verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
