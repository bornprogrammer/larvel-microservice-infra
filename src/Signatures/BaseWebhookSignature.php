<?php

namespace Laravel\Infrastructure\Signatures;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Laravel\Infrastructure\Log\Logger;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

abstract class BaseWebhookSignature implements SignatureValidator
{
    protected string $webhookClientSecret;

    public function __construct()
    {
        $this->webhookClientSecret = env("WEBHOOK_CLIENT_SECRET");
    }

    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $isSignatureVerified = false;
        try {
            $this->storeTheWebhookRequest($request);
            $isSignatureVerified = $this->isSignatureVerified($request, $config);
            if ($isSignatureVerified) {
                $this->onSignatureVerified($request, $config);
            } else {
                $this->onSignatureNotVerified($request, $config);
            }
        } catch (\Exception $exception) {
            $this->storeTheWebhookRequest($request);
        }
        return $isSignatureVerified;
    }

    protected function storeTheWebhookRequest(Request $request)
    {
        // not 100% sure
        ///store the record to webhook payload
    }

    abstract function isSignatureVerified(Request $request, WebhookConfig $config): bool;

    abstract function onSignatureVerified(Request $request, WebhookConfig $config): void;

    abstract function onSignatureNotVerified(Request $request, WebhookConfig $config): void;
}
