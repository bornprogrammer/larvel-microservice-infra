<?php

namespace Laravel\Infrastructure\Events;

use Laravel\Infrastructure\Jobs\Traits\RetainBearerTokenTrait;

abstract class BaseQueueEvent extends BaseEvent
{
  use RetainBearerTokenTrait;

  protected function beforeDispatch(): void
  {
    parent::beforeDispatch();
    $this->retainBearerToken();
  }
}
