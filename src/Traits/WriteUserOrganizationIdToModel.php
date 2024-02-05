<?php

namespace Laravel\Infrastructure\Traits;

use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

trait WriteUserOrganizationIdToModel
{
    public function writeUserOrgId(Model $model): void
    {
        $userOrgIdColName = property_exists($model, "userOrgIdColName") ? $model->userOrgIdColName : "user_org_id";
        $model->{$userOrgIdColName} = RequestSessionFacade::getUserOrgIdFromQueryStrElseFromToken();
    }
}
