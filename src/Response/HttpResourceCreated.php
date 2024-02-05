<?php

namespace Laravel\Infrastructure\Response;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class HttpResourceCreated extends HttpResponse
{
    public function __construct(array $data, ?string $message = null)
    {
        $message = $message ?? "Resource created successfully";
        parent::__construct($data, $message, HttpStatusCodeConstant::RESOURCE_CREATED);
    }
}
