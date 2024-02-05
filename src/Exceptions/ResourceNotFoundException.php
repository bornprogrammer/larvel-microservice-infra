<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class ResourceNotFoundException extends HttpResponseException
{
    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Resource Not Found";
        parent::__construct($message ?? "Resource Not Found", HttpStatusCodeConstant::RESOURCE_NOT_FOUND, $previous);
    }
}
