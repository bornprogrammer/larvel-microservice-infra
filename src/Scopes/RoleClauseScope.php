<?php

namespace Laravel\Infrastructure\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Infrastructure\Helpers\RoleHelper;

trait RoleClauseScope
{
    use CreatedByClauseScope, OrgIdClauseScope;

    public function scopeRoleClause(Builder $builder, string $createdByColumnName = "created_by", string $orgColumnName = "organization_id"): ?Builder
    {
        $builder = $builder->orgIdClause($orgColumnName);
        if (RoleHelper::isUserPlatformUser()) {
            return $builder->createdByClause($createdByColumnName);
        }
        return null;
    }
}
