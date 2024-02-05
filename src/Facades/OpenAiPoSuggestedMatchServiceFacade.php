<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class OpenAiPoSuggestedMatchServiceFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return "openAi_po_suggested_match_service";
    }
}
