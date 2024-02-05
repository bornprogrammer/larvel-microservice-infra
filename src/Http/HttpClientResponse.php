<?php

namespace Laravel\Infrastructure\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;
use Laravel\Infrastructure\Exceptions\HttpClientErrorException;
use Laravel\Infrastructure\Exceptions\HttpResponseException;
use Laravel\Infrastructure\Log\Logger;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

class HttpClientResponse
{
    protected readonly Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function throwExceptionIfAnyHttpErrorElseData(string $exceptionMessage = "There is some internal error"): array|Throws|null
    {
        if ($this->response->successful()) {
            return $this->getJsonResponse();
        }
        $this->throwExceptionFromResponse();
    }

    public function throwExceptionIf404ElseData(string $exceptionMessage = ""): array|Throws|null
    {
        if ($this->response->successful()) {
            return $this->getJsonResponse();
        } else if ($this->response->status() === HttpStatusCodeConstant::RESOURCE_NOT_FOUND) {
            $this->throwExceptionFromResponse();
        }
        return null;
    }

    /**
     * Undocumented function
     *
     * @param string $exceptionMessage
     * @return array|Throws|null
     */
    public function throwExceptionIfServerErrorElseData(string $exceptionMessage = ""): array|Throws|null
    {
        if ($this->response->serverError()) {
            $this->throwExceptionFromResponse();
        }
        return $this->getJsonResponse();
    }

    public function getJsonResponse(): ?array
    {
        return $this->response->json();
    }

    public function getResponseObject(): Response
    {
        return $this->response;
    }

    public function getStatusCode(): string|int
    {
        return $this->response->status();
    }

    protected function throwExceptionFromResponse(): void
    {
        throw new HttpClientErrorException("There is some error", $this->response->status(), null, $this->getJsonResponse());
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}
