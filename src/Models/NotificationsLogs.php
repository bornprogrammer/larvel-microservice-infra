<?php

namespace Laravel\Infrastructure\Models;

use Laravel\Infrastructure\Models\BaseModel;
use Laravel\Infrastructure\Scopes\RoleClauseScope;

class NotificationsLogs extends BaseModel
{
    protected $table = 'push_notification_logs';

    protected $fillable = ["fcm_token", "response"];
}
