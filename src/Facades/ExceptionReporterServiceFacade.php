<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class ExceptionReporterServiceFacade extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return "exception_reporter_service";
    }
}
