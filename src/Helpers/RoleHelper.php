<?php

namespace Laravel\Infrastructure\Helpers;

use Laravel\Infrastructure\Constants\RoleTypeConstants;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

class RoleHelper
{
    public static function isUserPlatformUser(): bool
    {
        return self::getRoleName() === RoleTypeConstants::ROLE_TYPE_PLATFORM_USER;
    }

    public static function getRoleId(): string
    {
        $roleDetails = RequestSessionFacade::getRoleDetails();
        return $roleDetails["id"] ?? null;
    }

    public static function getRoleName(): ?string
    {
        $roleDetails = RequestSessionFacade::getRoleDetails();
        return $roleDetails["name"] ?? null;
    }

    public static function isUserFinanceApprover(): bool
    {
        return self::getRoleName() === RoleTypeConstants::ROLE_TYPE_FINANCE_APPROVER;
    }

    public static function isUserOrgAdmin(): bool
    {
        return self::getRoleName() === RoleTypeConstants::ROLE_TYPE_ORG_ADMIN;
    }
}
