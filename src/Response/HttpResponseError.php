<?php

namespace Laravel\Infrastructure\Response;

use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;
use Laravel\Infrastructure\Exceptions\HttpResponseException;
use Laravel\Infrastructure\Helpers\ArrayHelper;

class HttpResponseError extends HttpResponse
{
    public bool $isError = true;
    public ?array $errorData;

    public function __construct(HttpResponseException $exception)
    {
        parent::__construct(null, $exception->getMessage(), $exception->getCode());
        if (ArrayHelper::isArrayValid($exception->errorData)) {
            $this->errorData = $exception->errorData;
        }
    }
}
