<?php

namespace Laravel\Infrastructure\PubSubs;

use Laravel\Infrastructure\Exceptions\SystemException;
use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;

class SnsWorkerLogger
{
  protected static $ins = null;
  protected int $count;
  public function __construct()
  {
    $this->count = 0;
  }

  public static function start()
  {
    if (!self::$ins) {
      self::$ins =  new SnsWorkerLogger();
      self::$ins->logToDB("worker started in " . config("app.app_name"));
    }
    if (++self::$ins->count === 100) {
      self::$ins->logToDB("worker still running " . config("app.app_name"));
      self::$ins->count = 0;
    }
  }

  public function logToDB($message): void
  {
    $exception = new SystemException($message);
    $exception->event_type = "worker";
    ExceptionReporterServiceFacade::saveToDB($exception);
  }
}
