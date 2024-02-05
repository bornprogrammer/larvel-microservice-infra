<?php

namespace Laravel\Infrastructure\DTOS;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Laravel\Infrastructure\Helpers\ArrayHelper;

class FetchNMergeResponseToCollectionDTO extends BaseDTO
{
    public readonly Collection $collection;
    public readonly string|array $collectionKey;
    public readonly string $responseKey;
    public readonly string $mergeKeyName;
    public readonly ?string $recursiveCollectionKey;
    public ?array $callable;

    public bool $isOneToMany;

    public static function from(Collection|LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection $collection, string|array|null $collectionKey, string $responseKey, string $mergeKeyName, ?string $recursiveCollectionKey = null, ?array $callable = null, bool $isOneToMany = false): self
    {
        return new self([
            "collection" => ArrayHelper::convertArrORElequentColOrLengthAwarePaginatorToSupportCollection($collection),
            "collectionKey" => $collectionKey,
            "responseKey" => $responseKey,
            "mergeKeyName" => $mergeKeyName,
            "recursiveCollectionKey" => $recursiveCollectionKey,
            "callable" => $callable,
            "isOneToMany" => $isOneToMany,
        ]);
    }

    public static function fromArray(array $config): self
    {
        return self::from($config['collection'], $config['collectionKey'], $config['responseKey'], $config['mergeKeyName'], $config['recursiveCollectionKey'] ?? null, $config['callable'] ?? null, $config['isOneToMany'] ?? false);
    }
}
