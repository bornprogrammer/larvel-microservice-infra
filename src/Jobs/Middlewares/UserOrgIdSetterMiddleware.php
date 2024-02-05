<?php

namespace Laravel\Infrastructure\Jobs\Middlewares;

use Laravel\Infrastructure\Helpers\UtilHelper;

class UserOrgIdSetterMiddleware
{

  public function handle($job, $next)
  {
    if (isset($job->sessionData)) {
      UtilHelper::setUserOrgIdAsQueryStrInCurrentRequest($job->sessionData['user_org_id']);
    }

    $next($job);
  }
}
