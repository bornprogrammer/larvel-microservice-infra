<?php

namespace Laravel\Infrastructure\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

trait CreatedByClauseScope
{
    use OrgIdClauseScope;

    public function scopeCreatedByClause(Builder $builder, string $columnName = "created_by"): Builder
    {
        return $builder->where($columnName, RequestSessionFacade::getUserOrgId());
    }

    public function scopeSelfClause(Builder $builder, string $createdByColumnName = "created_by", string $orgColumnName = "organization_id"): ?Builder
    {
        $builder = $builder->orgIdClause($orgColumnName);
        return $builder->where($createdByColumnName, RequestSessionFacade::getUserOrgId());
    }
}
