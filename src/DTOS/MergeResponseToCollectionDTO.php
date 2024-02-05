<?php

namespace Laravel\Infrastructure\DTOS;

use Illuminate\Support\Collection;

class MergeResponseToCollectionDTO extends BaseDTO
{
    public readonly ?Collection $collection;
    public readonly string|array $collectionKey;
    public readonly string $responseKey;
    public readonly ?array $response;
    public readonly string $mergeKeyName;

    public static function fromFetchNMergeResponseToCollectionDTO(FetchNMergeResponseToCollectionDTO $mergeResponseToCollectionDTO, ?array $response): self
    {
        return new self([
            "collection" => $mergeResponseToCollectionDTO->collection,
            "collectionKey" => $mergeResponseToCollectionDTO->collectionKey,
            "responseKey" => $mergeResponseToCollectionDTO->responseKey,
            "response" => $response,
            "mergeKeyName" => $mergeResponseToCollectionDTO->mergeKeyName,
        ]);
    }
}
