<?php

namespace Laravel\Infrastructure\Clients;

use Laravel\Infrastructure\DTOS\BaseDTO;

interface Client
{
    public function transformPayload(?array $payload = []): array|null;

    public function transformResponse(?array $response = []): array|null;

    public function call(array|null|BaseDTO $payload = []): array|null;
}
