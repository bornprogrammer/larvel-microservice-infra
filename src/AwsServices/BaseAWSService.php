<?php

namespace Laravel\Infrastructure\AwsServices;

use Aws\AwsClient;
use Aws\Credentials\Credentials;
use Laravel\Infrastructure\ThirdPartySDK\BaseThirdPartySDK;

class BaseAWSService extends BaseThirdPartySDK
{
    protected AwsClient $awsClient;
    protected static $awsClientType;
    protected string $baseUrl;
    public function __construct(string $configName = "awsS3Bucket")
    {
        parent::__construct(config($configName));
    }
    protected function initiateSDK()
    {
        $awsClientType = static::$awsClientType;
        $this->awsClient = new $awsClientType([
            'version' => 'latest',
            'region' => $this->sdkConfiguration['region'],
            'credentials' => new Credentials($this->sdkConfiguration['key'], $this->sdkConfiguration['secret']),
            'retries' => 3
        ]);
    }

    public function process(...$params)
    {
        try {
            return $this->execute($params);
        } catch (\Throwable $th) {
            $this->handleSDKException($th, $params);
        }
    }

    protected function execute(string|array ...$params)
    {
    }
}
