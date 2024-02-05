<?php

namespace Laravel\Infrastructure\Traits;

use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

trait WriteOrganizationIdToModel
{
    public function writeOrgId(Model $model): void
    {
        $organizationIdColName = property_exists($model, "organizationIdColName") ? $model->organizationIdColName : "organization_id";
        $model->{$organizationIdColName} = RequestSessionFacade::getOrgIdFromQueryStrElseFromToken();
    }
}
