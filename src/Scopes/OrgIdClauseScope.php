<?php

namespace Laravel\Infrastructure\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

trait OrgIdClauseScope
{
    public function scopeOrgIdClause(Builder $builder, string $columnName = "organization_id"): Builder
    {
        return $builder->where($columnName, RequestSessionFacade::getOrgId());
    }
}
