<?php

namespace Arcadia\Bundle\AuthorizationBundle\Exception;

use Throwable;

class BadPayloadFormatException extends \LogicException
{
    public const NO_USERNAME = 1;
    public const NO_ENCODED_TOKEN = 2;
    public const BAD_EXPIRATION_DATE_RAW_FORMAT = 3;
    public const BAD_TOKEN_FORMAT = 4;

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct("Bad payload format.", $code, $previous);
    }
}