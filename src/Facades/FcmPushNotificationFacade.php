<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class FcmPushNotificationFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return "fcm_push_notifi_service";
    }
}
