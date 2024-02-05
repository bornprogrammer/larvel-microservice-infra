<?php

namespace Laravel\Infrastructure\Middlewares;

use Illuminate\Http\Request;

class FeatureAuthorization
{
  /**
   * Handle an incoming request.
   *
   */
  public function handle(Request $request, \Closure $next)
  {
  }

  protected function isAllowedByFeature(): bool
  {
    return true;
  }

  protected function getFeatureConfig()
  {
  }

  protected function getVersion()
  {
  }
}
