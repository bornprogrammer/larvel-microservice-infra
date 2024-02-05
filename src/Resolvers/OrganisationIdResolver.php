<?php

namespace Laravel\Infrastructure\Resolvers;

use Laravel\Infrastructure\Facades\RequestSessionFacade;
use OwenIt\Auditing\Contracts\Resolver;
use OwenIt\Auditing\Contracts\Auditable;

class OrganisationIdResolver implements Resolver
{
    /**
     * @return string
     */
    public static function resolve(Auditable $auditable): ?string
    {
        return RequestSessionFacade::getOrgIdFromQueryStrElseFromToken();
    }
}
