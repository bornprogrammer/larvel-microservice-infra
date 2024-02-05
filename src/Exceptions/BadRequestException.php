<?php

namespace Laravel\Infrastructure\Exceptions;

use Dflydev\DotAccessData\Data;
use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class BadRequestException extends HttpResponseException
{
    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Bad Request error";
        parent::__construct($message, HttpStatusCodeConstant::BAD_REQUEST, $previous);
    }
}
