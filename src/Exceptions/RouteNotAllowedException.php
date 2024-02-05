<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class RouteNotAllowedException extends HttpResponseException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Please check your route which seems invalid";
        parent::__construct("Please check your route which seems invalid", HttpStatusCodeConstant::RESOURCE_NOT_FOUND, $previous);
    }
}
