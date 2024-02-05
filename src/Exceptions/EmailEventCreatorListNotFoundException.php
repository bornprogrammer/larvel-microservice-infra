<?php

namespace Laravel\Infrastructure\Exceptions;

class EmailEventCreatorListNotFoundException extends SystemException
{

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("Event Creator List not found for sending an email", $code, $previous);
    }
}
