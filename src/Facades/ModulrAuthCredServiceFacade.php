<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class ModulrAuthCredServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "modulrauthcredservice";
    }
}
