<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class CardRequestExceptions extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(string $message = null, int $code, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Some Error";
        parent::__construct($message ?? $message, $code, $previous);
    }
}
