<?php

namespace Laravel\Infrastructure\Exceptions;

class EmailRecipientsNotFoundException extends SystemException
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("No recipients found", $code, $previous);
    }
}
