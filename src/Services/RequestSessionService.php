<?php

namespace Laravel\Infrastructure\Services;

use Laravel\Infrastructure\Helpers\ArrayHelper;

class RequestSessionService extends BaseService
{
    protected ?array $userDetails;
    protected ?array $roleDetails;

    public function __construct()
    {
        $this->initiateSessionData();
    }

    public function setUserPayload(): void
    {
        $authorizedToken = $this->getAuthorizationToken();

        if ($authorizedToken) {
            $authorizedToken = explode(".", $authorizedToken);
            if (isset($authorizedToken[1])) {
                $this->userDetails = json_decode(base64_decode($authorizedToken[1]), true);
                $this->userDetails = json_decode($this->userDetails['custom_claim'], true);
                $this->roleDetails = $this->userDetails["role"];
            }
        }
    }

    public function initiateSessionData(): void
    {
        $this->userDetails = [];
        $this->roleDetails = [];
        $this->setUserPayload();
    }

    public function getRoleDetails(): ?array
    {
        $roleData = ArrayHelper::isArrayValid($this->roleDetails) ? $this->roleDetails : [];
        return $roleData;
    }

    public function getAuthorizationToken(): ?string
    {
        $authorization = request()->header("Authorization");
        return $authorization;
    }

    public function getUserId(): ?string
    {
        $userId = ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"]["id"] : null;
        return $userId;
    }
    public function getUserOrgId(): ?string
    {
        $userOrgId = ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"]["user_org_id"] : null;
        return $userOrgId;
    }

    public function getOrgId(): ?string
    {
        $orgId = ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"]["organization_id"] : null;
        return $orgId;
    }

    public function getOrgName(): ?string
    {
        $orgId = ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"]["organization_name"] : "";
        return $orgId;
    }

    public function getUserFirstName(): ?string
    {
        $userDetails = ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"] : null;
        if ($userDetails) {
            $userDetails = $userDetails['first_name'];
            return $userDetails;
        }
        return null;
    }

    public function getUserFullName(): ?string
    {
        $userDetails = ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"] : null;
        if ($userDetails) {
            $userDetails = $userDetails['first_name'] . " " . $userDetails['middle_name'] . " " . $userDetails['last_name'];
            return $userDetails;
        }
        return null;
    }

    public function getUserOrgIdFromQueryStrElseFromToken(): ?string
    {
        $userOrgIdFromQueryStr = request()->query("session_user_org_id");
        $userOrgId = $userOrgIdFromQueryStr ?? (ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"]["user_org_id"] : null);
        return $userOrgId;
    }

    public function getOrgIdFromQueryStrElseFromToken(): ?string
    {
        $orgIdFromQueryStr = request()->query("session_organization_id");
        $orgId = $orgIdFromQueryStr ?? (ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"]["organization_id"] : null);
        return $orgId;
    }

    public function getLoginUserDetails(): ?string
    {
        $getLoginUserDetails = ArrayHelper::isArrayValid($this->userDetails) ? $this->userDetails["user"] : null;
        return json_encode($getLoginUserDetails);
    }
}
