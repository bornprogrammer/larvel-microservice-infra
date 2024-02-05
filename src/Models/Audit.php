<?php

namespace Laravel\Infrastructure\Models;

use Carbon\Carbon;
use Laravel\Infrastructure\Traits\ApplyAuditFilterTrait;
use Illuminate\Database\Eloquent\Model;

class Audit extends \OwenIt\Auditing\Models\Audit
{
    use ApplyAuditFilterTrait;

    public function getCreatedAtAttribute($value)
    {
        return (new Carbon($value))->setTimezone('Europe/London')->format('d/m/Y H:i');
    }


    public function getUpdatedAtAttribute($value)
    {
        return (new Carbon($value))->setTimezone('Europe/London')->format('d/m/Y H:i');
    }
}
