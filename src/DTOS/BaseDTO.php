<?php

namespace Laravel\Infrastructure\DTOS;

use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Helpers\UtilHelper;
use Laravel\Infrastructure\Traits\JsonSerialization;
use Spatie\DataTransferObject\DataTransferObject;

class BaseDTO extends DataTransferObject
{
    use JsonSerialization;

    public function convertKeysToSlug(): array
    {
        $props = $this->toArray();
        $newProps = [];
        if (ArrayHelper::isArrayValid($props)) {
            foreach ($props as $key => $val) {
                $newProps[UtilHelper::fromLowerCamelCaseToSnakeCase($key)] = $val;
            }
        }
        return $newProps;
    }
}
