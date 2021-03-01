<?php

namespace Arcadia\Bundle\AuthorizationBundle\Exception;

use Throwable;

class DecodingException extends \RuntimeException
{
    public const PAYLOAD_BASE64_DECODING_FAILURE = 1;
    public const PAYLOAD_UNSERIALIZING_FAILURE = 2;
    public const TOKEN_DECODING_FAILURE = 3;

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct("Decoding failure.", $code, $previous);
    }
}