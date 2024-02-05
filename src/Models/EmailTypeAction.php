<?php

namespace Laravel\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTypeAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $table = "email_type_actions";
}
