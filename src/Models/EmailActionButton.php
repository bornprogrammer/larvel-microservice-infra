<?php

namespace Laravel\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailActionButton extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $table = "email_action_buttons";
}
