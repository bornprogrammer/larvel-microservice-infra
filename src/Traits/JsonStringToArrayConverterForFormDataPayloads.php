<?php

namespace Laravel\Infrastructure\Traits;

trait JsonStringToArrayConverterForFormDataPayloads
{
    use PayloadTransformer;
    protected function transform(): array
    {
        $jsonStringKeys = $this->getJsonStringKeys();
        $arrayData = [];
        foreach ($jsonStringKeys as  $value) {
            $arrayData[$value] = json_decode($this->request->get($value), true);
        }
        return $arrayData;
    }

    protected abstract function getJsonStringKeys(): array;
}
