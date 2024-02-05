<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class FileNotUploadException extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Unprocessable Entity";
        parent::__construct($message ?? "Unprocessable Entity", HttpStatusCodeConstant::UNPROCESSABLE_ENTITY, $previous);
    }
}
