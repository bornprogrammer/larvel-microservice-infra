<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class EmailServiceV2Facade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return "email_service_v2";
    }
}
