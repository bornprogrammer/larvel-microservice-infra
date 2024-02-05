<?php

namespace Laravel\Infrastructure\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Infrastructure\Events\BaseEvent;
use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Helpers\UtilHelper;
use Laravel\Infrastructure\Jobs\Traits\UniqueJobTrait;

abstract class BaseQueueListener extends BaseListener implements ShouldQueue, ShouldBeUnique
{
  public $tries = 5;

  public $maxExceptions = 2;
  public array $queueConfig;

  use UniqueJobTrait, SerializesModels, Queueable;

  public function __construct()
  {
    parent::__construct();
    $this->beforeDispatch(); // listener also get instantiated before dispatching the event
  }

  protected function beforeDispatch(): void
  {
    $this->setQueueConfig();
  }

  protected function setQueueConfig(): void
  {
    $this->queueConfig = config('queue.general');
  }

  protected function setUniqueRequestId(): void
  {
    UtilHelper::setRequestId();
  }

  public function shouldQueue(): bool
  {
    return $this->queueConfig['enabled'];
  }

  protected function preExecute($event): void
  {
    parent::preExecute($event);
    $this->setBearerTokenToHeader();
    $this->setQueueConfig();
    $this->setUniqueRequestId();
    RequestSessionFacade::initiateSessionData();
  }

  protected function setBearerTokenToHeader()
  {
    $token = $this->event->token;
    UtilHelper::setWithoutBearerTokenInHeaderForCurrentRequest($token);
  }

  // The number of seconds to wait before retrying the job.
  public function backoff(): int
  {
    // as backoff will be executed before handle
    return config('queue.general')['backoff'];
  }

  protected function execute(): void
  {
    $this->executeQueueJob();
  }

  public abstract function executeQueueJob();

  public function failed(BaseEvent $event, $exception)
  {
    try {
      ExceptionReporterServiceFacade::report($exception);
    } catch (\Throwable $th) {
    }
  }
}
