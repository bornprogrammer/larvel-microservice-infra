<?php

namespace Laravel\Infrastructure\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Laravel\Infrastructure\Helpers\ArrayHelper;

class HttpClientService
{
    protected string $url;

    protected array $queryString;

    protected array $headers;

    protected array $pathParams;

    protected PendingRequest $httpObject;

    public function __construct()
    {
        $this->init();
    }

    /**
     * will be used by the base class who will build the object using builder pattern
     *
     * @return HttpClientService
     */
    public static function getIns(): HttpClientService
    {
        return new HttpClientService();
    }

    protected function init(): HttpClientService
    {
        $this->url = "";
        $this->queryString = [];
        $this->headers = [];
        $this->pathParams = [];
        $this->httpObject = Http::retry(1, 30000);
        return $this;
    }

    public function setBaseURL(string $url): HttpClientService
    {
        $this->url = $url;
        return $this;
    }

    public function setURLPath(string $path): HttpClientService
    {
        $this->pathParams[] = $path;
        return $this;
    }

    public function setURLPaths(array $paths): HttpClientService
    {
        $this->pathParams = [...$this->pathParams, ...$paths];
        return $this;
    }

    /**
     *
     */
    public function setHeader(string $key, string $val): HttpClientService
    {
        $this->headers[$key] = $val;
        return $this;
    }

    public function setContentTypeAppJson(): HttpClientService
    {
        $this->httpObject->contentType("application/json");
        return $this;
    }

    public function setBearerToken(string $token, string $tokenType = "Bearer"): HttpClientService
    {
        return $this->setHeader("Authorization", $tokenType . " " . $token);
    }

    public function setBasicAuth(string $clientId, string $clientSecret): HttpClientService
    {
        return $this->setHeader("Authorization", base64_encode($clientId . ":" . $clientSecret));
    }

    public function setHeaders(array $headers): HttpClientService
    {
        $this->headers = [...$this->headers, ...$headers];
        return $this;
    }

    public function setQueryString(array $queryString): HttpClientService
    {
        $this->queryString = [...$this->queryString, ...$queryString];
        return $this;
    }

    public function get(): HttpClientResponse
    {
        $this->setContentTypeAppJson();
        $queryString = $this->queryString ? ["query" => $this->queryString] : [];
        return $this->call("get", $queryString);
    }

    public function post(array $payload = []): HttpClientResponse
    {
        $this->setContentTypeAppJson();
        $jsonBody = ArrayHelper::isArrayValid($payload) ? ["json" => $payload] : $payload;
        return $this->call("post", $jsonBody);
    }

    public function patch(array $payload = []): HttpClientResponse
    {
        $this->setContentTypeAppJson();
        $jsonBody = ["json" => $payload];
        return $this->call("patch", $jsonBody);
    }

    public function put(array $payload): HttpClientResponse
    {
        $this->setContentTypeAppJson();
        $jsonBody = ["json" => $payload];
        return $this->call("put", $jsonBody);
    }

    public function delete(array|null $payload = null): HttpClientResponse
    {
        $this->setContentTypeAppJson();
        $jsonBody = ArrayHelper::isArrayValid($payload) ? ["json" => $payload] : [];
        return $this->call("delete", $jsonBody);
    }

    public function postFile(string $filePath): HttpClientResponse
    {
        $fullURL = $this->buildPathIfAny();
        $fileContent = file_get_contents($filePath);
        $fileExtension = explode(".", $filePath);
        $fileExtension = $fileExtension[count($fileExtension) - 1];
        $this->httpObject = $this->httpObject->withHeaders($this->headers);
        $response = $this->httpObject->attach("attachment", $fileContent, "attachment." . $fileExtension)->post($fullURL);
        $this->init();
        return new HttpClientResponse($response);
    }

    protected function call(string $method, array $options = []): HttpClientResponse
    {
        $fullURL = $this->buildPathIfAny();
        $this->httpObject = $this->httpObject->withHeaders($this->headers);
        $response = $this->httpObject->send($method, $fullURL, $options);
        $this->init();
        return new HttpClientResponse($response);
    }

    private function buildPathIfAny(): ?string
    {
        return $this->pathParams ? $this->url . "/" . implode("/", $this->pathParams) : $this->url;
    }
}
