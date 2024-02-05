<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class MethodNotAllowedException extends HttpResponseException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Method is not allowed";
        parent::__construct("Method is not allowed", HttpStatusCodeConstant::METHOD_NOT_ALLOWED, $previous);
    }
}
