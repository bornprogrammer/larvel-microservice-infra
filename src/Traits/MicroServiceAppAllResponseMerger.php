<?php

namespace Laravel\Infrastructure\Traits;

use Illuminate\Support\Collection;
use Laravel\Infrastructure\DTOS\FetchNMergeResponseToCollectionDTO;
use Laravel\Infrastructure\DTOS\MergeResponseToCollectionDTO;

trait MicroServiceAppAllResponseMerger
{
    use MicroServiceAppResponseMerger;
    public function fetchAllNMergeResponseToCollection(FetchNMergeResponseToCollectionDTO $collectionDTO): ?Collection
    {
        $response = $this->fetchResponseByCollection($collectionDTO->collection, $collectionDTO->collectionKey, $collectionDTO->mergeKeyName, $collectionDTO->recursiveCollectionKey, $collectionDTO->callable);
        return $this->mergeResponseToCollection(MergeResponseToCollectionDTO::fromFetchNMergeResponseToCollectionDTO($collectionDTO, $response), $collectionDTO->recursiveCollectionKey);
    }
}
