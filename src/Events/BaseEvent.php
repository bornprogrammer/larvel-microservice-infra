<?php

namespace Laravel\Infrastructure\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class BaseEvent
{
  use Dispatchable, InteractsWithSockets;

  public function __construct(public $data)
  {
    $this->beforeDispatch();
  }

  protected function beforeDispatch(): void
  {
  }
}
