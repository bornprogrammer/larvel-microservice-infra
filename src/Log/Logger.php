<?php

namespace Laravel\Infrastructure\Log;

use Illuminate\Support\Facades\Log;
use Laravel\Infrastructure\Helpers\EnvironmentHelper;

class Logger
{
    public static $logChannel;

    public static function getLogChannel()
    {
        if (!self::$logChannel) {
            // stderr,stack
            $channel = EnvironmentHelper::isLocal() ? "stderr" : "stack";
            //            $channel="stderr";
            self::$logChannel = Log::channel($channel);
        }
        return self::$logChannel;
    }

    public static function info(...$messages): void
    {
        if (EnvironmentHelper::isLocal()) {
            foreach ($messages as $message) {
                self::getLogChannel()->info($message);
            }
        }
    }

    public static function debug($message, array $context = []): void
    {
        self::getLogChannel()->debug($message, $context);
    }

    public static function error($message, array $context = []): void
    {
        self::getLogChannel()->error($message, $context);
    }
}
