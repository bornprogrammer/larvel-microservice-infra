<?php

namespace Laravel\Infrastructure\Enums;

enum UserOrgStatusEnum: string
{
    case Active = "active";
    case Inactive = "inactive";
    case Deleted = "deleted";
    case Disabled = "disabled";
    case Invited = "invited";
    case Verified = "verified";

    // makethis code in constants
}
