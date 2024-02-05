<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class ConflictException extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Record does already exists";
        parent::__construct($message ?? $this->defaultMessage, HttpStatusCodeConstant::CONFLICT, $previous);
    }
}
