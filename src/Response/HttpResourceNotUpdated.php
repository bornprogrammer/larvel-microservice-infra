<?php

namespace Laravel\Infrastructure\Response;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class HttpResourceNotUpdated extends HttpResponse
{
    public function __construct(?array $data, ?string $message = null)
    {
        $message = $message ?? "Resource not updated successfully";
        parent::__construct($data, $message, HttpStatusCodeConstant::BAD_REQUEST);
    }
}
