<?php

namespace Laravel\Infrastructure\Jobs\Middlewares;

class GeneralMiddleware
{

  public function handle($job, $next)
  {
    // setting bearer token if available
    if (method_exists($job, "retainBearerToken") && $job->token) {
      request()->instance()->headers->set('Authorization', $job->token);
    }
    $next($job);
  }
}
