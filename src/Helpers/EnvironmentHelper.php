<?php

namespace Laravel\Infrastructure\Helpers;

use Laravel\Infrastructure\Constants\EnvironmentConstants;

class EnvironmentHelper
{

    public static function getEnvironmentName(): string
    {
        return config("app.env");
    }

    public static function isProduction(): bool
    {
        return config("app.env") === EnvironmentConstants::ENVIRONMENT_PRODUCTION;
    }

    public static function isStaging(): bool
    {
        return config("app.env") === EnvironmentConstants::ENVIRONMENT_STAGING;
    }

    public static function isLocal(): bool
    {
        return config("app.env") === EnvironmentConstants::ENVIRONMENT_LOCAL;
    }

    public static function isDevelopment(): bool
    {
        return config("app.env") === EnvironmentConstants::ENVIRONMENT_DEVELOPMENT;
    }

    public static function isTesting(): bool
    {
        return config("app.env") === EnvironmentConstants::ENVIRONMENT_TEST;
    }

    public static function isDemo(): bool
    {
        return config("app.env") === EnvironmentConstants::ENVIRONMENT_DEMO;
    }
}
