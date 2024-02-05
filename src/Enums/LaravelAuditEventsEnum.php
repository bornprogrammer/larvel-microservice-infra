<?php

namespace Laravel\Infrastructure\Enums;

enum LaravelAuditEventsEnum: string
{
    case Created = "created";

    case Updated = "updated";

    case Deleted = "deleted";

    case Restored = "restored";
}
