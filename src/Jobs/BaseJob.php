<?php

namespace Laravel\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Infrastructure\Jobs\Middlewares\GeneralMiddleware;
use Laravel\Infrastructure\Jobs\Traits\RetainBearerTokenTrait;
use Laravel\Infrastructure\Jobs\Traits\UniqueJobTrait;

/**
 * @deprecated version
 */
abstract class BaseJob implements ShouldQueue, ShouldBeUnique
{
  protected array|null|Model|Collection $data;
  public $tries = 3;
  use RetainBearerTokenTrait, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UniqueJobTrait;

  public function __construct(array|null|Model|Collection $data)
  {
    $this->data = $data;
    $this->postDispatch();
  }

  protected function postDispatch(): void
  {
    $this->retainRequestData();
  }

  protected function retainRequestData(): void
  {
    if (method_exists($this, "setRequestData")) {
      $this->setRequestData();
    }
    if (method_exists($this, "setSessionData")) {
      $this->setSessionData();
    }
    $this->retainBearerToken();
  }

  public function middleware(): array
  {
    return [new GeneralMiddleware];
  }
}
