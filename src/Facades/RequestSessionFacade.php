<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class RequestSessionFacade extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return "requestsession";
    }
}
