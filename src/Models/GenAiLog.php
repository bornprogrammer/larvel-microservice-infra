<?php

namespace Laravel\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenAiLog extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'gen_ai_logs';

    protected $guarded = [];
}
