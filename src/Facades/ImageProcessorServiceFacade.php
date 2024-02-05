<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class ImageProcessorServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "image_processor_service";
    }
}
