<?php

namespace Laravel\Infrastructure\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\DTOS\FetchNMergeResponseToCollectionDTO;
use Laravel\Infrastructure\DTOS\MergeResponseToCollectionDTO;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use stdClass;

trait MicroServiceAppResponseMerger
{
    public function fetchNMergeResponseToCollection(FetchNMergeResponseToCollectionDTO $collectionDTO): ?Collection
    {
        $response = $this->fetchResponseByCollection($collectionDTO->collection, $collectionDTO->collectionKey, $collectionDTO->mergeKeyName, $collectionDTO->recursiveCollectionKey, $collectionDTO->callable);
        return $this->mergeResponseToCollection(MergeResponseToCollectionDTO::fromFetchNMergeResponseToCollectionDTO($collectionDTO, $response), $collectionDTO->recursiveCollectionKey, $collectionDTO->isOneToMany);
    }

    /**
     * @desc will be used in a case when user will pass the service name dynamically in query string so that callie can avoid the dirty checking.
     * @param FetchNMergeResponseToCollectionDTO $collectionDTO
     * @return Collection|null
     */
    public function fetchNMergeResponseToCollectionIfRequested(FetchNMergeResponseToCollectionDTO $collectionDTO): ?Collection
    {
        $serviceName = $this->setMicroAppServiceName();
        $requestedServices = explode(",", request()->query("services", ""));
        if (in_array($serviceName, $requestedServices)) {
            return $this->fetchNMergeResponseToCollection($collectionDTO);
        }
        return $collectionDTO->collection;
    }

    protected function mergeResponseToCollection(MergeResponseToCollectionDTO $mergeResponseToCollectionDTO, ?string $recursiveCollectionKey = null, bool $isOneToMany = false): ?Collection
    {
        $collection = null;
        if ($mergeResponseToCollectionDTO->collection->isNotEmpty()) {
            $collection = $mergeResponseToCollectionDTO->collection;
            $responseArray = $this->buildResponseArrayByCollectionKeyValue($mergeResponseToCollectionDTO->response, $mergeResponseToCollectionDTO->responseKey, $isOneToMany);

            foreach ($collection as $collectionItem) {
                $this->traverseThroughCollectionItemByCollectionKey($collectionItem, explode(".", $mergeResponseToCollectionDTO->collectionKey), function ($collectionKeyName, $collectionValue) use ($responseArray, $mergeResponseToCollectionDTO) {
                    $this->bindResponseItemToCollectionItem($collectionValue, $collectionKeyName, $responseArray, $mergeResponseToCollectionDTO->mergeKeyName);
                });
                if ($recursiveCollectionKey) {
                    $this->traverseThroughCollectionItemByCollectionKeyRecursively($collectionItem, explode(".", $mergeResponseToCollectionDTO->collectionKey), $recursiveCollectionKey, function ($collectionKeyName, $collectionValue) use ($responseArray, $mergeResponseToCollectionDTO) {
                        $this->bindResponseItemToCollectionItem($collectionValue, $collectionKeyName, $responseArray, $mergeResponseToCollectionDTO->mergeKeyName);
                    });
                }
            }
        }
        return $collection;
    }

    private function bindResponseItemToCollectionItem(stdClass|array|Model|null &$collectionItem, string $collectionKeyName, ?array $responseArray, string $mergeKeyName): void
    {
        $collectionItemId = $this->getValueFromCollectionItemByKey($collectionItem, $collectionKeyName);
        $responseArrayItem = $responseArray[$collectionItemId] ?? null;
        if ($collectionItem instanceof stdClass) {
            $collectionItem->{$mergeKeyName} = $responseArrayItem;
        } else {
            $collectionItem[$mergeKeyName] = $responseArrayItem;
        }
    }

    protected function fetchResponseByCollection(Collection $collection, string $collectionKey, string $mergeKeyName, ?string $recursiveCollectionKey, ?callable $callable = null): ?array
    {
        $extractedOutIds = $this->extractOutAllIdFromCollection($collection, $collectionKey, $recursiveCollectionKey, $callable);
        if (ArrayHelper::isArrayValid($extractedOutIds)) {
            $result = $this->getResponseByMultipleIds(implode(",", $extractedOutIds), $mergeKeyName);
            return $result;
        }
        return null;
    }

    protected function getResponseByMultipleIds(string $commaSeperatedIds, ?string $mergeKeyName = null): ?array
    {
        $results = $this->setURLPath($commaSeperatedIds)->get()->getJsonResponse();
        return $results;
    }

    protected function extractOutAllIdFromCollection(Collection $collection, string $collectionKey, ?string $recursiveCollectionKey, ?callable $callable = null): array
    {
        $extractedOutIds = [];
        if ($collection->isNotEmpty()) {
            $collectionKey = explode(".", $collectionKey);
            foreach ($collection as $collectionItem) {
                if ($callable) {
                    if (call_user_func($callable, ArrayHelper::convertObjectToArray($collectionItem))) {
                        goto mycode;
                    } else {
                        continue;
                    }
                } else {
                    goto mycode;
                }
                mycode:
                $ids = [];
                $this->extractOutSingleIdFromCollectionItem($collectionItem, $collectionKey, $ids);
                $ids1 = [];
                if ($recursiveCollectionKey) {
                    $ids1 = $this->extractOutSingleIdFromCollectionItemRecursively($collectionItem[$recursiveCollectionKey], $collectionKey, $recursiveCollectionKey);
                }
                $extractedOutIds = [...$extractedOutIds, ...$ids, ...$ids1];
            }
        }
        return array_unique($extractedOutIds);
    }

    protected function extractOutSingleIdFromCollectionItem(\Illuminate\Database\Eloquent\Collection|stdClass|array|Model|null $collectionItem, array $collectionKey, array &$idsContainer): void
    {
        $this->traverseThroughCollectionItemByCollectionKey($collectionItem, $collectionKey, function ($collectionKeyName, $collectionItem) use (&$idsContainer) {
            $extractedId =  $this->getValueFromCollectionItemByKey($collectionItem, $collectionKeyName);
            if ($extractedId) {
                $idsContainer[] = $extractedId;
            }
        });
    }

    protected function getValueFromCollectionItemByKey(stdClass|\Illuminate\Database\Eloquent\Collection|array|Model|null $collectionItem, string $keyName)
    {
        return $collectionItem instanceof stdClass ? $collectionItem->{$keyName} : $collectionItem[$keyName];
    }

    /**
     * @desc will traverse through each collection item by $collectionKey such as user.details.id
     * @param stdClass|array|Model|null $collectionItem
     * @param array $collectionKey
     * @param $callable
     * @return void
     */
    protected function traverseThroughCollectionItemByCollectionKey(stdClass|\Illuminate\Database\Eloquent\Collection|array|Model|null &$collectionItem, array $collectionKey, $callable): void
    {
        if (ArrayHelper::isArrayValid($collectionKey)) {
            $keyName = array_shift($collectionKey);
            // $data = $collectionItem instanceof stdClass ? $collectionItem->{$keyName} :  $collectionItem[$keyName];
            if (is_array($collectionItem)) {
                $data = &$collectionItem[$keyName];
            } else {
                $data  = $collectionItem->{$keyName};
            }
            if (count($collectionKey) === 0) {
                $callable($keyName, $collectionItem);
            } else if ($data instanceof \Illuminate\Database\Eloquent\Collection) {
                foreach ($data as $dataItem) {
                    $this->traverseThroughCollectionItemByCollectionKey($dataItem, $collectionKey, $callable);
                }
            } else if ($data instanceof stdClass || $data instanceof Model) {
                $this->traverseThroughCollectionItemByCollectionKey($data, $collectionKey, $callable);
            } else if (is_array($data) && ArrayHelper::isArrayValid($data)) {
                $this->traverseThroughCollectionItemByCollectionKey($data, $collectionKey, $callable);
            }
        }
    }

    protected function extractOutSingleIdFromCollectionItemRecursively(stdClass|array|Model|null|\Illuminate\Database\Eloquent\Collection $collectionItem, array $collectionKey, ?string $recursiveCollectionKey): array
    {
        $idsContainer = [];
        $this->traverseThroughCollectionItemByCollectionKeyRecursively($collectionItem, $collectionKey, $recursiveCollectionKey, function ($collectionKeyName, $collectionItem) use (&$idsContainer) {
            $idsContainer[] = $collectionItem[$collectionKeyName];
        });
        return $idsContainer;
    }

    protected function traverseThroughCollectionItemByCollectionKeyRecursively(stdClass|array|Model|null|\Illuminate\Database\Eloquent\Collection $collectionItem, array $collectionKey, ?string $recursiveCollectionKey, $callable): void
    {
        if ($collectionItem instanceof \Illuminate\Database\Eloquent\Collection) {
            foreach ($collectionItem as $item) {
                $this->traverseThroughCollectionItemByCollectionKey($item, $collectionKey, $callable);

                $this->traverseThroughCollectionItemByCollectionKeyRecursively($item, $collectionKey, $recursiveCollectionKey, $callable);
            }
        } else if ($collectionItem[$recursiveCollectionKey]) {
            //            $this->traverseThroughCollectionItemByCollectionKey($collectionItem[$recursiveCollectionKey], $collectionKey, $callable);
            $this->traverseThroughCollectionItemByCollectionKeyRecursively($collectionItem[$recursiveCollectionKey], $collectionKey, $recursiveCollectionKey, $callable);
        }
    }

    protected function buildResponseArrayByCollectionKeyValue(?array $response, string $responseKey = "id", bool $isOneToMany = false): array
    {
        $builtResponse = [];
        if (ArrayHelper::isArrayValid($response)) {
            foreach ($response as $item) {
                if ($isOneToMany) {
                    $builtResponse[$item[$responseKey]][] = $item;
                } else {
                    $builtResponse[$item[$responseKey]] = $item;
                }
            }
        }
        return $builtResponse;
    }
}
