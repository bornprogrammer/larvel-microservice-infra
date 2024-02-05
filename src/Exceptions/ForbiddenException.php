<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class ForbiddenException extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Forbidden";
        parent::__construct($message ?? "Forbidden", HttpStatusCodeConstant::UNAUTHORIZED, $previous);
    }
}
