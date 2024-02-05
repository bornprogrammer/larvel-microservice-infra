<?php

namespace Laravel\Infrastructure\PubSubs;

use Laravel\Infrastructure\Exceptions\PubSubFailedException;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Helpers\UtilHelper;
use Laravel\Infrastructure\Log\SessionLogger;

abstract class BasePubSubscriber
{
  /**
   * @param string  $subject   SNS Subject
   * @param array   $payload   JSON decoded 'Message'
   */
  public function __construct(protected string $subject, protected array $payload)
  {
  }

  public function handle(): void
  {
    SessionLogger::start(__METHOD__, $this->payload);
    $this->beforeExecute();
    $this->executeProcessors();
    $this->afterExecute();
    SessionLogger::logToDB(__METHOD__, $this->payload);
  }

  protected function beforeExecute(): void
  {
    SessionLogger::start(__METHOD__);
    $this->setBearerTokenToHeader();
    SessionLogger::end(__METHOD__);
  }

  protected function setBearerTokenToHeader()
  {
    SessionLogger::start(__METHOD__);
    $token = $this->payload['token'] ?? null;
    if ($token) {
      unset($this->payload['token']);
      UtilHelper::setWithoutBearerTokenInHeaderForCurrentRequest($token);
    }
    RequestSessionFacade::initiateSessionData();
    SessionLogger::end(__METHOD__);
  }

  protected function executeProcessors()
  {
    SessionLogger::start(__METHOD__);
    $processors = $this->setProcessors();
    try {
      foreach ($processors as  $processor) {
        app()->make($processor)->process($this->payload);
      }
      SessionLogger::end(__METHOD__);
    } catch (\Throwable $th) {
      SessionLogger::end(__METHOD__);
      throw new PubSubFailedException($th, $this->payload);
    }
  }

  protected abstract function setProcessors(): array;

  protected function afterExecute(): void
  {
  }
}
