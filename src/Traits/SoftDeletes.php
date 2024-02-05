<?php

namespace Laravel\Infrastructure\Traits;

use Illuminate\Database\Eloquent\SoftDeletes as LaravelSoftDeletes;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

trait SoftDeletes
{
    use LaravelSoftDeletes;

    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScope);
    }
}
