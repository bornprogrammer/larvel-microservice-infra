<?php

namespace Laravel\Infrastructure\Middlewares;

use Illuminate\Http\Request;
use Laravel\Infrastructure\Exceptions\ForbiddenException;
use Laravel\Infrastructure\Log\Logger;

class KlooAuthorization
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, \Closure $next)
    {
        $token = trim($request->bearerToken());
        #token validation
        if (empty($token)) {
            throw new ForbiddenException('Token is empty.');
        }

        #token validation for JWT
        $payload = $this->getHeader($token);
        if (!isset($payload->typ) || $payload->typ != 'JWT') {
            throw new ForbiddenException('Token is not valid JWT Token.');
        }

        $tokenParts = explode(".", $token);
        $tokenHeader = base64_decode($tokenParts[1]);
        $tkn_arr = (json_decode($tokenHeader, true));

        #custom claim validation
        if (isset($tkn_arr['custom_claim']) && is_string($tkn_arr['custom_claim'])) {
            if (empty(trim($tkn_arr['custom_claim'])))
                throw new ForbiddenException('Custom Claim is empty.');
        } else {
            throw new ForbiddenException('Either Custom Claim is not passed or Custom Claim is not a string.');
        }

        $cst_str = json_decode($tkn_arr['custom_claim']);

        #scope validation


        if (isset($cst_str->scopes) && is_array($cst_str->scopes)) {
            if (empty($cst_str->scopes))
                throw new ForbiddenException('Scope is empty.');
        } else {
            throw new ForbiddenException('Either Scope is not passed or Scope is not an array.');
        }

        $routeInfo = \Route::current();
        $action = $routeInfo->action;
        $name = substr($action['as'], 0, strpos($action['as'], '-', strripos($action['as'], '-')));
        #first string is given
        $scope = $cst_str->scopes;

        if (!in_array($name, $scope)) {
            throw new ForbiddenException('Forbidden Access.');
        }

        return $next($request);
    }

    private function getHeader(string $jwt)
    {
        $tks = explode('.', $jwt);

        if (count($tks) !== 3) {

            throw new ForbiddenException('Wrong number of segments');
        }
        list($headb64) = $tks;
        if (
            null === ($header = json_decode($this->urlsafeB64Decode($headb64)))
        ) {
            throw new ForbiddenException('Wrong number of segments');
        }
        return $header;
    }

    private function urlSafeB64Decode(string $input): string
    {

        $padLen = 4 - strlen($input) % 4;
        $input .= str_repeat('=', $padLen);
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
