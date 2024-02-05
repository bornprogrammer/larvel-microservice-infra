<?php

namespace Laravel\Infrastructure\Exceptions;

class QueueFailedException extends SystemException
{
  public $event_type = "queue";
}
