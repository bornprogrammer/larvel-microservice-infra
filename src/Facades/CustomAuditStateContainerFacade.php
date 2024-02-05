<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class CustomAuditStateContainerFacade extends Facade
{
  protected static function getFacadeAccessor(): string
  {
    return "custom_audit_state_container";
  }
}
