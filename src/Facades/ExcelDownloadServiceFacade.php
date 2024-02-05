<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class ExcelDownloadServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "excel_download_service";
    }
}
