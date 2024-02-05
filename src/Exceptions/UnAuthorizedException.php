<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class UnAuthorizedException extends HttpResponseException
{
    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "UnAuthorized";
        parent::__construct($message ?? "UnAuthorized", HttpStatusCodeConstant::UNAUTHORIZED, $previous);
    }
}
