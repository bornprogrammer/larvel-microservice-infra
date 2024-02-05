<?php

namespace Laravel\Infrastructure\Exceptions;

class PubSubFailedException extends SystemException
{
  public $event_type = "pub-subscriber";

  public function __construct(\Throwable $throwable, $payload = null)
  {
    parent::__construct($throwable->getMessage(), 0);
    $this->errorData = $payload;
  }
}
