<?php

namespace Laravel\Infrastructure\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Laravel\Infrastructure\Helpers\ArrayHelper;

abstract class BaseJsonResource extends JsonResource
{
    //    public static $wrap = false;
    public final function toArray($request)
    {
        if (is_array($this->resource)) {
            $result = $this->resource;
        } else if (method_exists($this->resource, 'toArray')) {
            $result = parent::toArray($request);
        } else {
            // for stdClass
            $result = json_decode(json_encode($this->resource, true), true);
        }
        if (ArrayHelper::isArrayValid($result)) {
            $result = $this->convertTo($result);
        }
        return $result;
    }

    abstract function convertTo(array $items): ?array;
}
