<?php

namespace Laravel\Infrastructure\Response;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;

class HttpResponseNotFound extends HttpResponse
{
    public function __construct(?array $data, ?string $message = null, string|int|null $code = null)
    {
        $code = $code ?? HttpStatusCodeConstant::RESOURCE_NOT_FOUND;
        $message = $message ?? "Response not Found";
        parent::__construct($data, $message, $code);
    }
}
