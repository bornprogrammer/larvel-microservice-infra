<?php

namespace Laravel\Infrastructure\Middlewares;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StrictJsonNumericCheck
{
    public function handle(Request $request, \Closure $next): JsonResponse
    {
        $data = $next($request);
        return $data->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
