<?php

namespace Laravel\Infrastructure\Response;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class HttpResourceNotCreated extends HttpResponse
{
    public function __construct(?array $data, ?string $message = null)
    {
        $message = $message ?? "Resource not created successfully";
        parent::__construct($data, $message, HttpStatusCodeConstant::UNPROCESSABLE_ENTITY);
    }
}
