<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class EmailServiceFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return "emailservice";
    }
}
