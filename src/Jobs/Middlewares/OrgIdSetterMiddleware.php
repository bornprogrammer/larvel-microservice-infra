<?php

namespace Laravel\Infrastructure\Jobs\Middlewares;

use Laravel\Infrastructure\Helpers\UtilHelper;

class OrgIdSetterMiddleware
{

  public function handle($job, $next)
  {
    if (isset($job->sessionData)) {
      UtilHelper::setOrgIdAsQueryStrInCurrentRequest($job->sessionData['org_id']);
    }

    $next($job);
  }
}
