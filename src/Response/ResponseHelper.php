<?php

namespace Laravel\Infrastructure\Response;

use Illuminate\Http\JsonResponse;
use Laravel\Infrastructure\Exceptions\HttpResponseException;

class ResponseHelper
{
    public static function sendResponse(HttpResponse|array $response): JsonResponse
    {
        $response = (array)$response;
        return response()->json($response, $response["statusCode"]);
    }

    public static function sendErrorResponse(HttpResponseException $exception): JsonResponse
    {
        $response = new HttpResponseError($exception);
        return self::sendResponse($response);
    }
}
