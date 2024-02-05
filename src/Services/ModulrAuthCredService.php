<?php

namespace Laravel\Infrastructure\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Infrastructure\Facades\RequestSessionFacade;

class ModulrAuthCredService extends BaseService
{
    protected array $modulrConfig;

    protected bool $isModulrConfigFetched;

    public function __construct()
    {
        $this->isModulrConfigFetched = false;
    }

    public function fetchModulrConfig(): self
    {
        if (!$this->isModulrConfigFetched) {
            $this->modulrConfig = [];
            $result = DB::table('organizations_modulr_details')
                ->where('organization_id', RequestSessionFacade::getOrgIdFromQueryStrElseFromToken())->first();
            if ($result) {
                $this->modulrConfig = ["modurl_auth_key" => Crypt::decrypt($result->modulr_auth_key), "modulr_secret_key" => Crypt::decrypt($result->modulr_auth_secret)];
            }
            $this->isModulrConfigFetched = true;
        }
        return $this;
    }

    public function getModulrAuthKey(): ?string
    {
        return $this->fetchModulrConfig()->modulrConfig["modurl_auth_key"] ?? null;
    }

    public function getModulrSecretKey(): ?string
    {
        return $this->fetchModulrConfig()->modulrConfig["modulr_secret_key"] ?? null;
    }

    public function resetFetchedModulrConfig()
    {
        $this->isModulrConfigFetched = false;
    }
}
