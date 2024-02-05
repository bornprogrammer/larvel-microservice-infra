<?php

namespace Laravel\Infrastructure\Resolvers;

use OwenIt\Auditing\Contracts\Resolver;
use OwenIt\Auditing\Contracts\Auditable;
use Laravel\Infrastructure\Helpers\UtilHelper;

class RequestIdResolver implements Resolver
{
    /**
     * @return string
     */
    public static function resolve(Auditable $auditable): string
    {
        return UtilHelper::getRequestId();
    }
}
