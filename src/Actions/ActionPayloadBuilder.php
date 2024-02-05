<?php

namespace Laravel\Infrastructure\Actions;

interface ActionPayloadBuilder
{
    public function build(array $params): array;
}
