<?php

namespace Laravel\Infrastructure\ThirdPartyServices;

use Laravel\Infrastructure\Http\HttpClientResponse;
use Laravel\Infrastructure\Http\HttpClientService;
use Laravel\Infrastructure\Log\Logger;

/**
 * can be extended by any class which is implementing any third party services
 */
abstract class BaseThirdPartyService
{
    protected HttpClientService $httpClientService;

    protected array $queryStrings;

    protected array $payload;

    protected string $baseURL;

    public function __construct(string $baseURL)
    {
        $this->httpClientService = HttpClientService::getIns();
        $this->baseURL = $baseURL;
    }

    protected function buildBaseURL(): string
    {
        $baseURL = $this->baseURL;
        $baseURL .= $this->setResourcePath() ? "/" . $this->setResourcePath() : "";
        return $baseURL;
    }

    protected abstract function setResourcePath(): string;

    protected abstract function preCall(): BaseThirdPartyService;

    protected function setURLPaths(array $paths): BaseThirdPartyService
    {
        $this->httpClientService->setURLPaths($paths);
        return $this;
    }

    protected function setHeaders(array $headers): BaseThirdPartyService
    {
        $this->httpClientService->setHeaders($headers);
        return $this;
    }

    protected function setQueryString(array $queryStrings): BaseThirdPartyService
    {
        $this->httpClientService->setQueryString($queryStrings);
        return $this;
    }

    /**
     * will be used to call the get method of any third party service
     *
     * @return HttpClientResponse
     */
    protected function get(): HttpClientResponse
    {
        return $this->preCall()->httpClientService->setBaseURL($this->buildBaseURL())->get();
    }

    /**
     * will be used to set the api key (secret) in case of missing bearer token
     *
     * @param string $val
     * @return BaseThirdPartyService
     */
    protected function setAPIKey(string $val): BaseThirdPartyService
    {
        $this->httpClientService->setHeader("Authorization", $val);
        return $this;
    }

    /**
     * will be used to set the bearer token
     *
     * @param string $token
     * @return BaseThirdPartyService
     */
    protected function setBearerToken(string $token): BaseThirdPartyService
    {
        $this->httpClientService->setBearerToken($token);
        return $this;
    }

    /**
     * will be used to set the bearer token
     *
     * @param string $authKey
     * @return BaseThirdPartyService
     */
    protected function setBasicAuthKey(string $authKey): BaseThirdPartyService
    {
        $this->setAPIKey("Basic " . $authKey);
        return $this;
    }

    /**
     * will be used to set the bearer token
     *
     * @param string $authKey
     * @return BaseThirdPartyService
     */
    protected function setBasicAuthKeyAsBase64Encoded(string $authKey): BaseThirdPartyService
    {
        $this->setAPIKey("Basic " . base64_encode($authKey));
        return $this;
    }

    protected function post(array $payload = []): HttpClientResponse
    {
        return $this->preCall()->httpClientService->setBaseURL($this->buildBaseURL())->post($payload);
    }

    protected function delete(): HttpClientResponse
    {
        return $this->preCall()->httpClientService->setBaseURL($this->buildBaseURL())->delete();
    }

    protected function patch(array $payload = []): HttpClientResponse
    {
        return $this->preCall()->httpClientService->setBaseURL($this->buildBaseURL())->patch($payload);
    }

    protected function postFile(string $filePath): HttpClientResponse
    {
        return $this->preCall()->httpClientService->setBaseURL($this->buildBaseURL())->postFile($filePath);
    }
}
