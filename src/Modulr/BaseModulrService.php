<?php

namespace Laravel\Infrastructure\Modulr;

use Laravel\Infrastructure\Facades\ModulrAuthCredServiceFacade;
use Laravel\Infrastructure\Helpers\DateHelper;
use Laravel\Infrastructure\ThirdPartyServices\BaseThirdPartyService;

abstract class BaseModulrService extends BaseThirdPartyService
{
    protected array $modulrConfig;

    protected bool $isModulrConfigEmpty;

    public function __construct()
    {
        $this->modulrConfig = config("modulr");
        $baseURL = $this->modulrConfig["modulr_api_url"];
        $this->isModulrConfigEmpty = true;
        parent::__construct($baseURL);
    }

    protected function setModulrAuthNSecretKeyFromDB(): self
    {
        $this->modulrConfig["modulr_authorization_key"] = ModulrAuthCredServiceFacade::getModulrAuthKey();
        $this->modulrConfig["modulr_authorization_secret"] = ModulrAuthCredServiceFacade::getModulrSecretKey();
        return $this;
    }

    protected function setModulrConfig(): void
    {
        if ($this->isModulrConfigEmpty) {
            $this->setModulrAuthNSecretKeyFromDB();
            $this->isModulrConfigEmpty = false;
        }
    }

    protected function setModulrConfigForSanctionCheck(): self
    {
        $this->isModulrConfigEmpty = false;
        return $this;
    }

    public function resetModulrConfig(): self
    {
        $this->isModulrConfigEmpty = true;
        ModulrAuthCredServiceFacade::resetFetchedModulrConfig();
        return $this;
    }

    protected function preCall(): self
    {
        $this->setAuthorizationSignature();
        return $this;
    }

    protected function setManagementTokenAsHeader(?string $managementToken): self
    {
        $this->setHeaders(["X-MOD-CARD-MGMT-TOKEN" => $managementToken]);
        return $this;
    }

    protected function setAuthorizationSignature(): void
    {
        $this->setModulrConfig();
        $authKey = $this->modulrConfig["modulr_authorization_key"];
        $authSecret = $this->modulrConfig["modulr_authorization_secret"];
        $gmtNow = DateHelper::getGMTNow("D, d M Y H:i:s e");
        $nonce = str_replace('.', '-', uniqid(null, true));
        $hmacStr = [
            'date: ' . $gmtNow,
            'x-mod-nonce: ' . $nonce,
        ];
        $hmacSignature = implode("\n", $hmacStr);
        $hmac = urlencode(base64_encode(hash_hmac('sha1', $hmacSignature, $authSecret, true)));
        $authorizationStr = 'Signature keyId="' . $authKey . '",algorithm="hmac-sha1",headers="date x-mod-nonce",signature="' . $hmac . '"';
        $this->setHeaders([
            'Date' => $gmtNow,
            'x-mod-nonce' => $nonce,
        ])->setAPIKey($authorizationStr)->setHeaders(["Accept" => "application/json"]);
    }
}
