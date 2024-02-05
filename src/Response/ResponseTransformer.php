<?php

namespace Laravel\Infrastructure\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Infrastructure\Constants\HttpMethodConstant;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Log\Logger;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResponseTransformer
{
    public function handle(Request $request, JsonResponse|Response|BinaryFileResponse $response): JsonResponse|BinaryFileResponse|Response|null
    {
        if ($response instanceof BinaryFileResponse) {
            return $response;
        } else {
            $data = $this->getDataInArray($response);
            $httpMethodName = $request->method();
            if ($this->isHttpErrorResponse($data)) {
                $response = $data;
            } else if ($this->isDataValid($data)) {
                $response = $this->buildResponse($httpMethodName, $data);
            } else {
                $response = $this->buildErrorResponse($httpMethodName, $data);
            }
            return ResponseHelper::sendResponse($response);
        }
    }

    // will tell if result is exception error
    private function isHttpErrorResponse(?array $data): bool
    {
        return isset($data['isError']) && $data['isError'] === true;
    }

    private function getDataInArray(JsonResponse|Response|\stdClass $response): array|null
    {
        $data = null;
        if ($response instanceof JsonResponse) {
            $data = $response->getData();
        } else if ($response instanceof Response && ArrayHelper::isArrayValid($response->getContent())) {
            $data = $response->getContent();
        }
        // if array element is stdObject not associative array
        if (empty($data) === false && is_object($data)) {
            $data = ArrayHelper::convertObjectToArray($data);
        }
        return $data;
    }

    private function buildResponse(string $httpMethodName, array|\stdClass $data): HttpResponse
    {
        $response = null;
        switch ($httpMethodName) {
            case HttpMethodConstant::GET:
                $response = new HttpResponseFound($data);
                break;
            case HttpMethodConstant::DELETE:
                $response = new HttpResourceDeleted($data);
                break;
            case HttpMethodConstant::PUT:
            case HttpMethodConstant::PATCH:
                $response = new HttpResourceUpdated($data);
                break;
            case HttpMethodConstant::POST:
                $response = new HttpResourceCreated($data);
                break;
        }
        return $response;
    }

    private function buildErrorResponse(string $httpMethodName, ?array $data = null): HttpResponse
    {
        $response = null;
        switch ($httpMethodName) {
            case HttpMethodConstant::GET:
                $statusCode = request()->query("statusCode");
                $response = new HttpResponseNotFound($data, null, $statusCode);
                break;
            case HttpMethodConstant::PUT:
            case HttpMethodConstant::PATCH:
            case HttpMethodConstant::DELETE:
                $response = new HttpResourceNotUpdated($data);
                break;
            case HttpMethodConstant::POST:
                $response = new HttpResourceNotCreated($data);
                break;
        }
        return $response;
    }

    private function isDataValid(array|\stdClass $data = null): bool
    {
        return empty($data) === false;
    }
}
