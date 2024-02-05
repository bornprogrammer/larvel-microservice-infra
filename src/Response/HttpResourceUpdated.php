<?php

namespace Laravel\Infrastructure\Response;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class HttpResourceUpdated extends HttpResponse
{
    public function __construct(array $data, ?string $message = null)
    {
        $message = $message ?? "Resource updated successfully";
        parent::__construct($data, $message, HttpStatusCodeConstant::SUCCESS);
    }
}
