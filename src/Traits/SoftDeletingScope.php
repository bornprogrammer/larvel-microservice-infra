<?php

namespace Laravel\Infrastructure\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletingScope as LaravelSoftDeletingScope;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

class SoftDeletingScope extends LaravelSoftDeletingScope implements Scope
{
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }

        $builder->onDelete(function (Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);
            return $builder->update([
                $column => $builder->getModel()->freshTimestampString(),
                "deleted_by" => RequestSessionFacade::getUserOrgId()
            ]);
        });
    }
}
