<?php

namespace Arcadia\Bundle\AuthorizationBundle\Exception;

use Throwable;

class TokenExpiredException extends \RangeException
{
    public function __construct($message = "Token expired.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}