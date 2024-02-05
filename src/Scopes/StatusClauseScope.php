<?php

namespace Laravel\Infrastructure\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Infrastructure\Constants\EntityStatusConstant;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

trait StatusClauseScope
{
    public function scopeActiveStatusClause(Builder $builder, string $columnName = "status"): Builder
    {
        return $builder->where($columnName, EntityStatusConstant::ACTIVE);
    }

    public function scopeNotDeletedStatusClause(Builder $builder, string $columnName = "status"): Builder
    {
        return $builder->whereIn($columnName, [EntityStatusConstant::ACTIVE, EntityStatusConstant::INACTIVE]);
    }
}
