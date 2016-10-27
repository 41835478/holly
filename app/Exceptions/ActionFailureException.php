<?php

namespace App\Exceptions;

use Exception;

class ActionFailureException extends Exception
{
    public function __construct($message = '操作失败！', $code = 470)
    {
        parent::__construct($message, $code);
    }
}
