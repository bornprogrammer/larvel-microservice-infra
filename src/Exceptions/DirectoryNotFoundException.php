<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class DirectoryNotFoundException extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "Directory Not Found";
        parent::__construct($message ?? "Directory Not Found", HttpStatusCodeConstant::RESOURCE_NOT_FOUND, $previous);
    }
}
