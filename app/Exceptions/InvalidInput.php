<?php

namespace App\Exceptions;

use Exception;

class InvalidInput extends Exception
{
    public function __construct($message = '非法输入！', $code = 421)
    {
        parent::__construct($message, $code);
    }
}
