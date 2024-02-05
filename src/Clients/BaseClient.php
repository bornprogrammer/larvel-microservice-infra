<?php

namespace Laravel\Infrastructure\Clients;

use Laravel\Infrastructure\Actions\Action;
use Laravel\Infrastructure\DTOS\BaseDTO;

class BaseClient implements Client
{
    public function __construct(protected readonly Action $action)
    {
    }
    /**
     *
     * @param array|null $payload
     * @return array|null
     */
    function transformPayload(?array $payload = []): array|null
    {
        return $payload;
    }

    /**
     *
     * @param array|null $response
     * @return array|null
     */
    function transformResponse(?array $response = []): array|null
    {
        return $response;
    }

    /**
     * @return array|null
     */
    public function call(array|null|BaseDTO $payload = []): array|null
    {
        $payload = $payload instanceof BaseDTO ? $payload->convertKeysToSlug() : $payload;
        $transformedPayload = $this->transformPayload($payload);
        $result = $this->action->handle($transformedPayload);
        $transformedResponse = $this->transformResponse($result);
        return $transformedResponse;
    }
}
