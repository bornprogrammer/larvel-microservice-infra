<?php

namespace Laravel\Infrastructure\Resolvers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class UserTypeResolver implements Resolver
{
    /**
     * @return string
     */
    public static function resolve(Auditable $auditable): string
    {
        return 'user';
    }
}
