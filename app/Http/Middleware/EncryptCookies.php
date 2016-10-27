<?php

namespace App\Http\Middleware;

use Holly\Http\Middleware\EncryptCookies as BaseEncrypter;

class EncryptCookies extends BaseEncrypter
{
    /**
     * The names of the cookies that should be encrypted.
     *
     * @var array
     */
    protected $only = [
        //
    ];
}
