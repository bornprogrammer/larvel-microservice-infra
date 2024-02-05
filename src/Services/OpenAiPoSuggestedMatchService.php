<?php

namespace Laravel\Infrastructure\Services;

use Laravel\Infrastructure\Models\GenAiLog;
use Illuminate\Http\UploadedFile;
use Laravel\Infrastructure\Exceptions\InternalServerErrorException;
use PDF;
use Illuminate\Support\Facades\Storage;
use Laravel\Infrastructure\Facades\AwsS3BucketServiceFacade;
use Laravel\Infrastructure\OpenAiServices\BaseOpenAiService;
use Laravel\Infrastructure\Facades\RequestSessionFacade;
use GuzzleHttp\Client;

class OpenAiPoSuggestedMatchService extends BaseService
{
    public function getSuggestedMatchList(array $params)
    {
        $resultData = [];
        $data = null;

        $url = $this->getGeneratedUrl($params);

        if (is_null($url)) {
            $this->saveLogs($params, $url, $data, 500);
            return $resultData = [
                "data" => null,
                "statusCode" => 500
            ];
        }

        try {
            $client = new Client();
            $headers = [
                'X-GenApi-Secret' => env("OPENAPI_SECRET"), // Replace with your desired GenApi-Secret
                'X-GenApi-name' => env("OPENAPI_KEY"), // Replace with your desired X-Genapi-name
            ];

            $response = $client->get($url, [
                'headers' => $headers,
            ]); // Replace with your desired URL
            // Get the response body as a string
            $data = $response->getBody()->getContents();
            // Process the response data as needed
            // ...
            // Optionally, you can get the status code and headers
            $statusCode = $response->getStatusCode();
            $headers = $response->getHeaders();

            if ($statusCode == 200) {
                $this->saveLogs($params, $url, $data, $statusCode);

                return $resultData = [
                    "data" => $data,
                    "statusCode" => $statusCode
                ];
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $this->saveLogs($params, $url, $data, $statusCode);

            return $resultData = [
                "data" => $data,
                "statusCode" => $statusCode
            ];
        }
    }

    public function getGeneratedUrl(array $params)
    {
        $orgId = $params['organization_id'] ?? null;
        $invoiceId = $params['invoice_id'] ?? null;
        $invoicePoNumber = $params['invoice_po_number'] ?? null;
        $baseURL = env("OPENAI_API_URL") ?? null;

        $url = $baseURL;

        if (!is_null($orgId) && !is_null($invoiceId) && !is_null($invoicePoNumber)) {
            $url .= "?organization_id=" . $orgId . "&invoice_id=" . $invoiceId . "&invoice_po_number=" . $invoicePoNumber;
        } elseif (!is_null($orgId) && !is_null($invoiceId) && is_null($invoicePoNumber)) {
            $url .= "?organization_id=" . $orgId . "&invoice_id=" . $invoiceId;
        } else {
            return null;
        }
        return $url;
    }

    public function saveLogs(array $params, $url, $data, $statusCode)
    {
        $logs = [
            'event_ref_id' => $params['invoice_id'] ?? null,
            'event_name' => env("OPENAPI_KEY") ?? null,
            'organization_id' => $params['organization_id'] ?? null,
            'status_code' => $statusCode,
            'request_url' => $url,
            'response_logs' => $data,
        ];
        GenAiLog::create($logs);
        return true;
    }
}
