<?php

namespace Laravel\Infrastructure\Jobs\Traits;

/** 
 * UniqueJobTrait.php
 */
trait RetainBearerTokenTrait
{
  public string $token;

  protected function retainBearerToken(): void
  {
    $this->token = request()->header("Authorization");
  }
}
