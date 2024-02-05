<?php

namespace Laravel\Infrastructure\MicroAppServices;

use Laravel\Infrastructure\DTOS\BaseDTO;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Helpers\ArrayHelper;
use Laravel\Infrastructure\Helpers\EnvironmentHelper;
use Laravel\Infrastructure\Http\HttpClientResponse;
use Laravel\Infrastructure\Http\HttpClientService;

abstract class BaseMicroAppServices
{
    protected array $serviceConfiguration;

    protected array $paths;

    protected array $headers;

    public function __construct(private readonly HttpClientService $httpClientService)
    {
        $this->initConfig();
        $this->initParams();
    }

    protected function initConfig(): void
    {
        $defaultConfig = config("microappservices.default");
        $envConfig = config("microappservices." . EnvironmentHelper::getEnvironmentName());
        $this->serviceConfiguration = ArrayHelper::mergeArrayRecursively([$defaultConfig, $envConfig]);
    }

    protected function setHeaders(array $headers): self
    {
        $this->httpClientService->setHeaders($headers);
    }

    protected function setVersion(): string
    {
        return "v1";
    }

    protected function initParams(): void
    {
        $this->paths = [$this->getServiceConfigByKey("path")];
    }

    protected function getServiceConfigByKey(string $key): string|array
    {
        return $this->serviceConfiguration["services"][$this->setMicroAppServiceName()][$key];
    }

    protected abstract function setMicroAppServiceName(): string;

    protected function post(array|BaseDTO $payload): MicroAppServicesResponse
    {
        $builtPayload = $this->buildPayload($payload);
        $response = $this->beforeCall()->httpClientService->post($builtPayload);
        $this->initParams();
        return $this->buildResponse($response);
    }

    protected function put(array|BaseDTO $payload): MicroAppServicesResponse
    {
        $response = $this->beforeCall()->httpClientService->put($this->buildPayload($payload));
        $this->initParams();
        return $this->buildResponse($response);
    }

    protected function patch(array|BaseDTO $payload): MicroAppServicesResponse
    {
        $response = $this->beforeCall()->httpClientService->patch($this->buildPayload($payload));
        $this->initParams();
        return $this->buildResponse($response);
    }

    protected function delete(array|BaseDTO|null $payload = null): MicroAppServicesResponse
    {
        $response = $this->beforeCall()->httpClientService->delete($this->buildPayload($payload));
        $this->initParams();
        return $this->buildResponse($response);
    }

    protected function get(): MicroAppServicesResponse
    {
        $response = $this->beforeCall()->httpClientService->get();
        $this->initParams();
        return $this->buildResponse($response);
    }

    protected function setURLPaths(array $paths): self
    {
        $this->paths = [...$this->paths, ...$paths];
        return $this;
    }

    protected function setURLPath(string $path): self
    {
        $this->setURLPaths([$path]);
        return $this;
    }

    protected function beforeCall(): self
    {
        $this->forwardBearerTokenIfAny()->httpClientService->setBaseURL($this->buildBaseURL())->setURLPaths($this->paths)->setContentTypeAppJson();
        return $this;
    }

    protected function forwardBearerTokenIfAny(): self
    {
        $token = RequestSessionFacade::getAuthorizationToken();
        if ($token) {
            $token = ["Authorization" => $token];
            $this->httpClientService->setHeaders($token);
        }
        return $this;
    }

    protected function buildBaseURL(): string
    {
        $baseUrl = $this->serviceConfiguration["base_url"];
        if (EnvironmentHelper::isLocal() || EnvironmentHelper::isTesting()) {
            $baseUrl .= ":" . $this->serviceConfiguration["services"][$this->setMicroAppServiceName()]["port"];
        }
        return $this->appendAPIPathWithVersion($baseUrl);
    }
    protected function appendAPIPathWithVersion(string $baseUrl): string
    {
        $apiPath = $this->serviceConfiguration["api_path"];
        $apiPath = explode("/", $apiPath)[0];
        $baseUrl .= "/" . $apiPath . "/" . $this->setVersion();
        return $baseUrl;
    }

    protected function buildPayload(array|BaseDTO $payload): ?array
    {
        return $payload instanceof BaseDTO ? $payload->convertKeysToSlug() : $payload;
    }

    protected function buildResponse(HttpClientResponse $httpClientResponse): MicroAppServicesResponse
    {
        return new MicroAppServicesResponse($httpClientResponse->getResponseObject());
    }
}
