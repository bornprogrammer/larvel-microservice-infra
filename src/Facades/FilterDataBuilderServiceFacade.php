<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class FilterDataBuilderServiceFacade extends Facade
{
  protected static function getFacadeAccessor(): string
  {
    return "filter_data_builder_service";
  }
}
