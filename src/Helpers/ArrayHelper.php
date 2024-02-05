<?php

namespace Laravel\Infrastructure\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArrayHelper
{
    public static function convertObjectToArray($obj): ?array
    {
        $val = null;
        if (empty($obj) === false && is_object($obj)) {
            $val = json_decode(json_encode($obj), true);
        }
        return $val;
    }

    public static function convertJsonStringToArray(?string $obj): ?array
    {
        $val = json_decode($obj);
        if (empty($val) === false && is_object($val)) {
            $val = self::convertObjectToArray($val);
        }
        return $val;
    }

    public static function isArrayValid($param): bool
    {
        return empty($param) === false && is_array($param);
    }

    public static function deleteArrayItemByValue(array &$array, $val): void
    {
        $arrayIndex = array_search($val, $array);
        if ($arrayIndex !== false) {
            unset($array[$arrayIndex]);
        }
    }

    public static function convertArrORElequentColOrLengthAwarePaginatorToSupportCollection(array|LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection $input): ?Collection
    {
        $collection = null;
        if (self::isArrayValid($input)) {
            $collection = collect($input);
        } else if ($input instanceof \Illuminate\Database\Eloquent\Collection) {
            $collection = collect($input->all());
        } else if ($input instanceof LengthAwarePaginator) {
            $collection = $input->toBase();
        } else if ($input instanceof Collection) {
            $collection = $input;
        }
        return $collection;
    }

    public static function mergeArrayRecursively(array $arrays): array
    {
        $result = array();
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                // Renumber integer keys as array_merge_recursive() does. Note that PHP
                // automatically converts array keys that are integer strings (e.g., '1')
                // to integers.
                if (is_integer($key)) {
                    $result[] = $value;
                } elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = self::mergeArrayRecursively(array(
                        $result[$key],
                        $value,
                    ));
                } else {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }

    public static function extractOutValueFrom2DArray(?array $array, string $keyName): array
    {
        if (ArrayHelper::isArrayValid($array)) {
            return array_column($array, $keyName);
        }
        return [];
    }

    public static function spliceArray(array $original, array $new, int $pos = -1): array
    {
        $splicedArray = [];
        if (self::isArrayValid($original) && self::isArrayValid($new)) {
            $pos = $pos === -1 ? count($original) : $pos;
            array_splice($original, $pos, 0, $new); // splice in at position 3
            $splicedArray = $original;
        }
        return $splicedArray;
    }

    public static function spliceAssocArray(array $original, array $new, int $pos = -1): array
    {
        $splicedArray = [];
        if (self::isArrayValid($original) && self::isArrayValid($new)) {
            $splicedArray  = array_slice($original, 0, $pos, true) +
                $new +
                array_slice($original, $pos, count($original) - $pos, true);
        }
        return $splicedArray;
    }
}
