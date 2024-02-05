<?php

namespace Laravel\Infrastructure\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Traits\UUIDTrait;
use OwenIt\Auditing\Contracts\Auditable;

class BaseModel extends Model implements Auditable
{
    use UUIDTrait;

    use \OwenIt\Auditing\Auditable;

    // leaving empty for future functinality

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            static::whenCreating($model);
        });
    }

    public function scopeOrgId(Builder $builder, ?string $orgId = null, ?string $field = null): Builder
    {
        $orgId = $orgId ?? RequestSessionFacade::getOrgIdFromQueryStrElseFromToken();
        $field = $field ?? "organization_id";
        return $builder->where($field, $orgId);
    }

    public function scopeUserOrgId(Builder $builder, ?string $userOrgId = null, ?string $field = null): Builder
    {
        $userOrgId = $userOrgId ?? RequestSessionFacade::getUserOrgIdFromQueryStrElseFromToken();
        $field = $field ?? "user_org_id";
        return $builder->where($field, $userOrgId);
    }

    protected static function whenCreating(Model $model)
    {
        $model->writeUUID($model);
        $writeOrgIdMethodName = "writeOrgId";
        if (method_exists($model, $writeOrgIdMethodName)) {
            $model->{$writeOrgIdMethodName}($model);
        }

        $writeUserOrgIdMethodName = "writeUserOrgId";
        if (method_exists($model, $writeUserOrgIdMethodName)) {
            $model->{$writeUserOrgIdMethodName}($model);
        }
    }
}
