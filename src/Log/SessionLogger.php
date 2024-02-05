<?php

namespace Laravel\Infrastructure\Log;

use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;

class SessionLogger
{
  private array $sessionLogs = [];

  protected static $ins = null;
  public function __construct()
  {
  }

  public static function from(): self
  {
    if (!self::$ins) {
      self::$ins =  new SessionLogger();
    }
    return self::$ins;
  }

  /**
   * @deprecated version
   *
   * @param [type] $message
   * @param [type] $payload
   * @return self
   */
  public function set($message, $payload = null): self
  {
    $this->sessionLogs[] = ['message' => $message, 'data' => $payload];
    return $this;
  }

  public static function start(string $methodName, $payload = null): void
  {
    $ins  = self::from();
    $ins->sessionLogs[] = ['message' => "starting point" . $methodName, 'data' => $payload];
  }

  public static function end(string $methodName, $payload = null): void
  {
    $ins  = self::from();
    $ins->sessionLogs[] = ['message' => "end point" . $methodName, 'data' => $payload];
  }

  public static function logToDB(string $methodName, $payload = null, $eventType = null): void
  {
    self::end($methodName . " message-processed", $payload);
    $exception  = new \Laravel\Infrastructure\Exceptions\SystemException("session-completed");
    $exception->event_type = $eventType;
    ExceptionReporterServiceFacade::saveToDB($exception, self::$ins->sessionLogs);
    self::$ins = null;
  }
}
