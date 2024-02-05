<?php

namespace Laravel\Infrastructure\Exceptions;

class CronFailedException extends SystemException
{
  public $event_type = "cron";
}
