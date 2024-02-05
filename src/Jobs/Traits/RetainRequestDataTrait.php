<?php

namespace Laravel\Infrastructure\Jobs\Traits;

/** 
 * UniqueJobTrait.php
 */
trait RetainRequestDataTrait
{
  public $requestData = [];

  protected function setRequestData(): void
  {
    $this->requestData = [
      "payload" => request()->all(),
      "query" => request()->query()
    ];
  }
}
