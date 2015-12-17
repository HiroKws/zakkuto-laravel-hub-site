<?php

namespace App\HubConnection\Exceptions;

use RuntimeException as BaseException;

class RuntimeException extends BaseException
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
