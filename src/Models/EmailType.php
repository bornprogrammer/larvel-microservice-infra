<?php

namespace Laravel\Infrastructure\Models;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailType extends BaseModel
{
    use HasFactory;

    protected $fillable = ["id", "slug", "event", "status", "belongs_to"];
}
