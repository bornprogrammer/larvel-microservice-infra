<?php

namespace Laravel\Infrastructure\Events;

abstract class BaseCustomAuditEvent
{
  public string $auditEvent; // card_issue

  public bool $isCustomEvent = true;

  public function __construct(string $auditEvent)
  {
    $this->auditEvent = $auditEvent;
  }
}
