<?php

namespace Laravel\Infrastructure\MicroAppServices;

use Laravel\Infrastructure\Exceptions\HttpClientErrorException;
use Laravel\Infrastructure\Http\HttpClientResponse;

class MicroAppServicesResponse extends HttpClientResponse
{
    protected function throwExceptionFromResponse(): void
    {
        $message = $this->getErrorMessage();
        throw new HttpClientErrorException($message, $this->getStatusCode(), null, null);
    }

    public function getJsonResponse(): ?array
    {
        $resp = parent::getJsonResponse();
        return $resp["result"] ?? null;
    }

    public function getErrorMessage(): array|string|null
    {
        return $this->response->json("message");
    }
}
