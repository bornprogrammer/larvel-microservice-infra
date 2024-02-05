<?php

namespace Laravel\Infrastructure\Exceptions;

class PublishTopicFailedException extends SystemException
{
  public $event_type = "publish-topic";

  public function __construct(\Throwable $throwable, $payload = null)
  {
    parent::__construct($throwable->getMessage(), 0);
    $this->errorData = $payload;
  }
}
