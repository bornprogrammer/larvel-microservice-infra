<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class InternalServerErrorException extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "There is some internal error";
        parent::__construct($message ?? $this->defaultMessage, HttpStatusCodeConstant::INTERNAL_SERVER_ERROR, $previous);
    }
}
