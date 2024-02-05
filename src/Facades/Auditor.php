<?php

namespace Laravel\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Laravel\Infrastructure\Contracts\AuditDriver auditDriver(\Laravel\Infrastructure\Contracts\Auditable $model);
 * @method static void execute(\Laravel\Infrastructure\Contracts\Auditable $model);
 */
class Auditor extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Laravel\Infrastructure\Auditor::class;
    }
}
