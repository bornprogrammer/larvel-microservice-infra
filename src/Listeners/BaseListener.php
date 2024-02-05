<?php

namespace Laravel\Infrastructure\Listeners;

use Laravel\Infrastructure\Events\BaseEvent;

abstract class BaseListener
{
  protected ?BaseEvent $event;

  public function __construct()
  {
    $this->event = null;
  }

  public function handle($event = null): void
  {
    // template method pattern
    $this->preExecute($event);
    $this->execute();
    $this->postExecute();
  }

  protected function postExecute(): void
  {
  }

  protected abstract function execute(): void;

  protected function preExecute($event): void
  {
    $this->event = $event;
  }
}
