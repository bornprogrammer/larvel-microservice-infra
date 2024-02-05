<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class AwsS3BucketServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "awss3bucketservice";
    }
}
