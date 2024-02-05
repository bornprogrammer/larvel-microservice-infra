<?php

namespace Laravel\Infrastructure\Jobs\Traits;

use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Log\Logger;

/** 
 * UniqueJobTrait.php
 */
trait SessionDataTrait
{
  public $sessionData = [];

  protected function setSessionData(): void
  {
    $this->sessionData = [
      "org_id" => RequestSessionFacade::getOrgIdFromQueryStrElseFromToken(),
      "user_org_id" => RequestSessionFacade::getUserOrgIdFromQueryStrElseFromToken(),
    ];
  }
}
