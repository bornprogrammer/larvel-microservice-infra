<?php

namespace Laravel\Infrastructure\Helpers;

use Illuminate\Support\Str;
use Laravel\Infrastructure\Constants\ClientType;
use Laravel\Infrastructure\Exceptions\SystemException;
use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;

class UtilHelper
{
    public static function fromLowerCamelCaseToSnakeCase(string $input): string
    {
        $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        preg_match_all($pattern, $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ?
                strtolower($match) :
                lcfirst($match);
        }
        return implode('_', $ret);
    }

    public static function setOrgIdAsQueryStrInCurrentRequest(string $org): void
    {
        request()->instance()->query->set('session_organization_id', $org);
    }

    public static function setUserOrgIdAsQueryStrInCurrentRequest(string $userOrgId): void
    {
        request()->instance()->query->set('session_user_org_id', $userOrgId);
    }

    // public static function setBearerTokenInHeaderForCurrentRequest(string $token, string $bearer = "Bearer "): void
    // {
    //     request()->instance()->headers->add(['Authorization' => "{$bearer}{$token}"]);
    // }

    public static function setWithoutBearerTokenInHeaderForCurrentRequest(string $token): void
    {
        self::setBearerTokenInHeaderForCurrentRequest($token, "");
    }
    public static function getRequestId(): string
    {
        $requestId = request()->header("Request-ID");
        return $requestId ?? Str::uuid()->toString();
    }

    public static function setRequestId(): void
    {
        $uuid = (string) Str::uuid();
        request()->headers->set('Request-ID', $uuid);
    }

    public static function ddQuery($query): void
    {
        $addSlashes = str_replace('?', "'?'", $query->toSql());
        $re = vsprintf(str_replace('?', '%s', $addSlashes), $query->getBindings());
        dd($re);
    }

    public static function setBearerTokenInHeaderForCurrentRequest(string $token): void
    {
        request()->instance()->headers->add(['Authorization' => "Bearer {$token}"]);
    }

    public static function throwCatchAndReportException($systemException): void
    {
        try {
            throw $systemException;
        } catch (\Throwable $th) {
            ExceptionReporterServiceFacade::report($th);
        }
    }

    public static function isRequestValidViaReferer(string $requestKeyName, string $requestValue): bool
    {
        $queryStringValue = self::getQueryStringValueViaViaReferer($requestKeyName);
        $isRequestValidViaReferer = $requestValue === $queryStringValue;
        return $isRequestValidViaReferer;
    }

    public static function getQueryStringValueViaViaReferer(string $requestKeyName): ?string
    {
        $queryStringValue = null;
        $referer = request()->header("referer");
        if ($referer) {
            parse_str(parse_url($referer, PHP_URL_QUERY), $array);
            $queryStringValue = $array[$requestKeyName] ?? null;
        }
        return $queryStringValue;
    }

    public static function getClientType(): ?string
    {
        return request()->header('client-type');
    }
}
