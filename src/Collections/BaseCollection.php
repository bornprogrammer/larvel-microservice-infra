<?php

namespace Laravel\Infrastructure\Collections;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Infrastructure\Helpers\ArrayHelper;

class BaseCollection extends Collection
{
    public function groupByNMergeCollection(string $groupByKey, string|array $keysToMerge, array $extraKeys = []): array
    {
        $data = [];
        $this->map(function ($item) use (&$data, $groupByKey, $keysToMerge, $extraKeys) {
            if (isset($item[$groupByKey])) {
                if (isset($data[$item[$groupByKey]])) {
                    $storedValue = $data[$item[$groupByKey]];
                    $storedValue[$keysToMerge] = [...$storedValue[$keysToMerge], $item[$keysToMerge]];
                    $data[$item[$groupByKey]] = $storedValue;
                } else {
                    $data[$item[$groupByKey]] = [$groupByKey => $item[$groupByKey]];
                    if (ArrayHelper::isArrayValid($extraKeys)) {
                        foreach ($extraKeys as $key) {
                            $data[$item[$groupByKey]][$key] = $item[$key];
                        }
                    }
                    $data[$item[$groupByKey]][$keysToMerge] = [$item[$keysToMerge]];
                }
            }
        });
        return array_values($data);
    }
}
