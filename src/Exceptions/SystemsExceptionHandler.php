<?php

namespace Laravel\Infrastructure\Exceptions;

use Illuminate\Http\JsonResponse;
use Laravel\Infrastructure\Constants\HttpStatusCodeConstant;
use Laravel\Infrastructure\Log\Logger;
use Laravel\Infrastructure\Response\ResponseHelper;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// will be used to send an exception only for php / laravel related error
class SystemsExceptionHandler
{
    protected static function handleSystemException(\Throwable $throwable): HttpResponseException
    {
        if ($throwable instanceof \PDOException) {
            if (self::isPDOUniqueConstraintException($throwable)) {
                $exception = new DBPDOException($throwable->getMessage(), HttpStatusCodeConstant::CONFLICT, $throwable);
            } else if (self::isPDOColumnMismatchedException($throwable)) {
                $exception = new DBPDOException($throwable->getMessage(), HttpStatusCodeConstant::INTERNAL_SERVER_ERROR, $throwable);
            } else {
                $exception = new DBPDOException($throwable->getMessage(), HttpStatusCodeConstant::INTERNAL_SERVER_ERROR, $throwable);
            }
        } else if ($throwable instanceof MethodNotAllowedHttpException) {
            $exception = new MethodNotAllowedException($throwable->getMessage(), 0, $throwable);
        } else if ($throwable instanceof ValidationException) {
            $exception = new BadRequestException($throwable->getMessage(), 0, $throwable);
        } else if ($throwable instanceof NotFoundHttpException) {
            $exception = new RouteNotAllowedException($throwable->getMessage(), 0, $throwable);
        } else {
            $exception = new InternalServerErrorException($throwable->getMessage(), 0, $throwable);
        }
        return $exception;
    }

    public static function render(\Throwable $throwable): JsonResponse
    {
        $exception = self::handleSystemException($throwable);
        return ResponseHelper::sendErrorResponse($exception);
    }

    /**
     * will be removed once every microservice extends the BaseHandler and comment the register method
     * @param \Throwable $throwable
     * @return JsonResponse
     */
    public static function handle(\Throwable $throwable): JsonResponse
    {
        $exception = self::handleSystemException($throwable);
        return ResponseHelper::sendErrorResponse($exception);
    }

    public static function report(\Throwable $throwable): void
    {
        $exception = self::handleSystemException($throwable);
        $exception->report(null);
    }

    private static function isPDOUniqueConstraintException(\Throwable $throwable): bool
    {
        return $throwable instanceof \PDOException && isset($throwable->errorInfo[1]) && $throwable->errorInfo[1] == 1062;
    }

    private static function isPDOColumnMismatchedException(\Throwable $throwable): bool
    {
        return $throwable instanceof \PDOException && $throwable->errorInfo[0] == "HY093";
    }
}
