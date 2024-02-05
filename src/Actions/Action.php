<?php

namespace Laravel\Infrastructure\Actions;

interface Action
{
    public function handle(?array $params = []): array|null;
}
