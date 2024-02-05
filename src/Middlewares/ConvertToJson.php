<?php

namespace Laravel\Infrastructure\Middlewares;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Infrastructure\Log\Logger;
use Laravel\Infrastructure\Response\ResponseTransformer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response;

class ConvertToJson
{
    public function handle(Request $request, \Closure $next): JsonResponse|BinaryFileResponse|Response|null
    {
        $data = $next($request);
        $responseTransformer = new ResponseTransformer();
        return $responseTransformer->handle($request, $data);
    }
}
