<?php

namespace Laravel\Infrastructure\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Infrastructure\Log\Logger;

abstract class BaseHandler extends ExceptionHandler
{
    public function register()
    {
        $this->renderable(function (\Throwable $e, $request) {
            return SystemsExceptionHandler::render($e);
        });
        $this->reportable(function (\Throwable $e) {
            SystemsExceptionHandler::report($e);
        });
    }
}
