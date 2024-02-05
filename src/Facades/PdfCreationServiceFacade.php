<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class PdfCreationServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "pdf_creation_service";
    }
}
