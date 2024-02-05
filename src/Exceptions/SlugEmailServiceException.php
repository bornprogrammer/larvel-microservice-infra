<?php

namespace Laravel\Infrastructure\Exceptions;

class SlugEmailServiceException extends SystemException
{
    public function __construct(\Throwable|SystemException $systemException)
    {
        parent::__construct($systemException->message, $systemException->code);
    }
}
