<?php

namespace Laravel\Infrastructure\Middlewares;


use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*

will be used for auditing purpose.
*/

class UniqueRequestId
{
    public function handle(Request $request, \Closure $next)
    {
        $uuid = (string) Str::uuid();
        $request->headers->set('Request-ID', $uuid);
        return $next($request);
    }
}
