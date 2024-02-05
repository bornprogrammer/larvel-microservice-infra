<?php

namespace Laravel\Infrastructure\Response;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class HttpResourceDeleted extends HttpResponse
{
    public function __construct(array $data, ?string $message = null)
    {
        $message = $message ?? "Resource deleted successfully";
        parent::__construct($data, $message, HttpStatusCodeConstant::NO_CONTENT);
    }
}
