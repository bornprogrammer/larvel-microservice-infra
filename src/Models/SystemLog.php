<?php

namespace Laravel\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemLog extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = ["user_org_id", "org_id", "role_name", "exception_type", "file_name", "line_number", "error_message", "event_type", "error_data", "user_name", "organization_name", "ms_name", "api", "api_payload"];
    // username,microservice name, stack trace, full url with body
}
