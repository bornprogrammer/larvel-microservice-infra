<?php

namespace Laravel\Infrastructure\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseJsonResourceAttributeTransformer
{
    abstract public static function transform($value);
}
