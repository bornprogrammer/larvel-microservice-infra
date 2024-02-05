<?php

namespace Laravel\Infrastructure\Resolvers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Log\Logger;
use OwenIt\Auditing\Contracts\Resolver;
use OwenIt\Auditing\Contracts\Auditable;

class UserOrgIdResolver implements Resolver
{
    /**
     * @return string
     */
    public static function resolve(Auditable $auditable): ?string
    {
        return RequestSessionFacade::getUserOrgIdFromQueryStrElseFromToken();
    }
}
