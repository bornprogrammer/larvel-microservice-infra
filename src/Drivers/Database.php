<?php

namespace Laravel\Infrastructure\Drivers;

use Illuminate\Support\Facades\Config;
use Laravel\Infrastructure\Contracts\Audit;
use Laravel\Infrastructure\Contracts\Auditable;
use Laravel\Infrastructure\Contracts\AuditDriver;

class Database implements AuditDriver
{
    /**
     * {@inheritdoc}
     */
    public function audit(Auditable $model): ?Audit
    {
        $implementation = Config::get('audit.implementation', \Laravel\Infrastructure\Models\Audit::class);

        return call_user_func([$implementation, 'create'], $model->toAudit());
    }

    /**
     * {@inheritdoc}
     */
    public function prune(Auditable $model): bool
    {
        if (($threshold = $model->getAuditThreshold()) > 0) {
            $forRemoval = $model->audits()
                ->latest()
                ->get()
                ->slice($threshold)
                ->pluck('id');

            if (!$forRemoval->isEmpty()) {
                return $model->audits()
                    ->whereIn('id', $forRemoval)
                    ->delete() > 0;
            }
        }

        return false;
    }
}
