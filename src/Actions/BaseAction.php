<?php

namespace Laravel\Infrastructure\Actions;


abstract class BaseAction implements Action
{
    protected $actionPayloadBuilder;

    protected $eventName;
    public function handle(?array $params = []): array|null
    {
        $payload = $this->preHandle($params);
        $result = [];
        if ($this->isHandleAble($payload)) {
            $result = $this->performAction($payload);
        }
        $result = $this->postHandle($params, $result, $payload);
        return $result;
    }

    protected function dispatchIfAllowed(?array $params, array $result, ?array $payload): void
    {
        if ($this->eventName && (!isset($params['is_dispatchable']) || $params['is_dispatchable'])) {
            $this->eventName::dispatch(['params' => $params, "result" => $result, "payload" => $params]);
        }
    }

    public function buildPayload(?ActionPayloadBuilder $builder, ?array $params): array
    {
        return $builder->build($params);
    }

    public function isHandleAble(?array $payload): bool
    {
        return true;
    }

    public function preHandle(?array $params): array
    {
        if ($this->actionPayloadBuilder) {
            return $this->buildPayload(app()->make($this->actionPayloadBuilder), $params);
        }
        return $params;
    }

    public function postHandle(?array $params, ?array $result, ?array $payload): array
    {
        $this->dispatchIfAllowed($params, $result, $payload);
        return $result;
    }

    protected abstract function performAction(?array $payload): array;
}
