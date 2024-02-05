<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class HttpClientErrorException extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(?string $message = "", int $code = 0, ?\Throwable $previous = null, mixed $errorData = null)
    {
        $code = $code > 0 ? $code : HttpStatusCodeConstant::INTERNAL_SERVER_ERROR;
        $this->defaultMessage = "There is an error while processing your request";
        parent::__construct($message ?? "There is an error while", $code, $previous);
        $this->errorData = $errorData;
    }
}
