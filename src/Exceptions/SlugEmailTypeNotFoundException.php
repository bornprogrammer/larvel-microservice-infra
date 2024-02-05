<?php

namespace Laravel\Infrastructure\Exceptions;

class SlugEmailTypeNotFoundException extends SystemException
{

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("Event type not found using the slug name", $code, $previous);
    }
}
