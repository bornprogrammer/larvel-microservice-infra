<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class SearchFilterServiceFacade extends Facade
{

  protected static function getFacadeAccessor(): string
  {
    return "search_filter_service";
  }
}
