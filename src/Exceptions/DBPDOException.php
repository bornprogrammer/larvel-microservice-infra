<?php

namespace Laravel\Infrastructure\Exceptions;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class DBPDOException extends HttpResponseException
{
    protected bool $showDefaultMessageByEnv = true;

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        $this->defaultMessage = "There is some db error while performing db operation";
        parent::__construct($message, $code ?? HttpStatusCodeConstant::INTERNAL_SERVER_ERROR, $previous);
    }
}
