<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class AwsSNSServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "aws_sns_service";
    }
}
