<?php

namespace Laravel\Infrastructure\ThirdPartySDK;

use Laravel\Infrastructure\Exceptions\InternalServerErrorException;
use Laravel\Infrastructure\Facades\ExceptionReporterServiceFacade;
use Laravel\Infrastructure\Helpers\ArrayHelper;

/**
 * can be extended by any class which is implementing any third party services
 */
abstract class BaseThirdPartySDK
{
    protected ?array $sdkConfiguration;

    /**
     * @throws InternalServerErrorException
     */
    public function __construct(?array $sdkConfiguration)
    {
        $this->initSDKConfiguration($sdkConfiguration);
        $this->initiateSDK();
    }

    protected abstract function initiateSDK();

    protected function initSDKConfiguration(?array $sdkConfiguration): void
    {
        $this->sdkConfiguration = $sdkConfiguration;
        if (!ArrayHelper::isArrayValid($this->sdkConfiguration)) {
            throw new InternalServerErrorException("SDK Configuration not found either in env file or in config");
        }
    }

    protected function handleSDKException(\Throwable $throwable, ?array $param = null): void
    {
        ExceptionReporterServiceFacade::report($throwable, $param);
    }
}
