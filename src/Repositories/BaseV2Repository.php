<?php

namespace Laravel\Infrastructure\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Models\BaseModel;
use Illuminate\Support\Str;

use function PHPUnit\Framework\callback;

abstract class BaseV2Repository
{
    protected $model;

    public function create(array $payload): BaseModel
    {
        $result = $this->model::create($payload);
        return $result;
    }

    public function createMany(array $payload, callable $callback = null): array
    {
        if (ArrayHelper::isArrayValid($payload)) {
            foreach ($payload as &$item) {
                $item['id'] =  Str::uuid()->toString();
                $item['created_at'] =  now();
                $item['updated_at'] =  now();
                if ($callback && is_callable($callback)) {
                    $item = callback($item);
                }
            }
            $this->model::insert($payload);
        } else {
            $payload = [];
        }
        return $payload;
    }

    public function deleteOne(string $id, string $fieldName = "id"): ?BaseModel
    {
        $data = $this->model::where($fieldName, $id)->first();
        if ($data) {
            $data->delete();
            return $data;
        }
        return null;
    }

    public function getDuplicate(array $duplicationClause, array $whereClause = []): Collection
    {
        $firstKey = key($duplicationClause);
        $result  = $this->model::where($firstKey, $duplicationClause[$firstKey]);
        return $result->get();
    }

    public function deleteMany(string|array $ids, string $fieldName = "id"): ?Collection
    {
        $ids = is_array($ids) ? $ids : [$ids];
        $data = $this->model::whereIn($fieldName, $ids)->get();
        if ($data->isNotEmpty()) {
            $ids = array_column($data->toArray(), "id");
            $this->model::whereIn("id", $ids)->delete();
            return $data;
        }
        return null;
    }

    /**
     *
     *
     * @param array $items [["id"=>"uuid",..any other keys]]
     * @param string $key key name inside array
     * @return array
     */
    public function deleteManyByArrays(array $items, string $key = "id"): array
    {
        if (ArrayHelper::isArrayValid($items)) {
            $ids = array_column($items, $key);
            $this->model::whereIn("id", $ids)->delete();
            return $items;
        }
        return $items;
    }

    public function updateById(string $id, array $payload, string $fieldName = "id"): ?BaseModel
    {
        $data = $this->model::where($fieldName, $id)->first();
        if ($data) {
            $data->update($payload);
            return $data;
        }
        return null;
    }

    public function getMany(array|string $ids, string $fieldName = "id", array $whereClause = []): Collection
    {
        $valueIds = is_array($ids) ? $ids : [$ids];
        $result = $this->model::whereIn($fieldName, $valueIds);
        if (ArrayHelper::isArrayValid($whereClause)) {
            foreach ($whereClause as $column => $value) {
                $result = $result->where($column, $value);
            }
        }
        $result = $result->get();
        return $result;
    }

    public function updateOptimistically(string $id, array $payload, string $fieldName = "id"): ?BaseModel
    {
        $data = $this->model::where($fieldName, $id)->first();
        if ($data) {
            $payload['version'] = $data->version++;
            $updatedCount = $data->where("version", $data->version)->update($payload);
            if ($updatedCount) {
            }
            return $data;
        }
        return null;
    }
}
