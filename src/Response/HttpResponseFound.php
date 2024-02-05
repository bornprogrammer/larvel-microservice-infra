<?php

namespace Laravel\Infrastructure\Response;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class HttpResponseFound extends HttpResponse
{
    public function __construct(array $data, ?string $message = null)
    {
        $message = $message ?? "Response Found";
        parent::__construct($data, $message, HttpStatusCodeConstant::SUCCESS);
    }
}
