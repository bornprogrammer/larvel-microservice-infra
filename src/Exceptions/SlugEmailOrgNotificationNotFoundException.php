<?php

namespace Laravel\Infrastructure\Exceptions;

class SlugEmailOrgNotificationNotFoundException extends SystemException
{

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("Event org notification not found", $code, $previous);
    }
}
