<?php

namespace Laravel\Infrastructure\Traits;

use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Illuminate\Support\Facades\Schema;

trait WriteAuthUserToLogColumns
{
    protected function initializeWriteAuthUserToLogColumns()
    {
        static::creating(function ($model) {
            if ($this->columnExists($model, 'created_by')) {
                $model->created_by = RequestSessionFacade::getUserOrgIdFromQueryStrElseFromToken();
            }
        });

        static::updating(function ($model) {
            if ($this->columnExists($model, 'updated_by')) {
                $model->updated_by = RequestSessionFacade::getUserOrgIdFromQueryStrElseFromToken();
            }
        });
    }

    private function columnExists($model, $column)
    {

        $tableName = $model->getTable();

        return Schema::hasColumn($tableName, $column);
    }
}
