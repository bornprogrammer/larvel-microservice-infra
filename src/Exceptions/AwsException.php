<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class AwsException extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(string $message = null, int $code, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Bad Request error";
        parent::__construct($message ?? "Bad Request error", $code, $previous);
    }
}
