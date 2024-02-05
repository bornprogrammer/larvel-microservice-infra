<?php

namespace Laravel\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Models\BaseModel;

class EmailLog extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $table = "email_logs";
}
