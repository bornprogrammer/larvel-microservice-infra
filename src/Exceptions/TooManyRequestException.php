<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class TooManyRequestException extends HttpResponseException
{
    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Too Many Request";
        parent::__construct($message ?? "Too Many Request", HttpStatusCodeConstant::TOO_MANY_REQUEST, $previous);
    }
}
