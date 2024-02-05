<?php

namespace Laravel\Infrastructure\Exceptions;

class EmailEventListNotFoundException extends SystemException
{

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("Event List not found for sending an email", $code, $previous);
    }
}
