<?php

namespace Laravel\Infrastructure\Signatures;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookConfig;

abstract class ModuleWebhookSignature extends BaseWebhookSignature
{
    public function isSignatureVerified(Request $request, WebhookConfig $config): bool
    {
        $date = $request->header('date');
        $xModeNonce = $request->header('x-mod-nonce');
        $signatureString = $this->getSignatureStringFromHeader($request->header($config->signatureHeaderName));
        return $this->matchSignature($request, $signatureString);
    }

    protected function storeTheWebhookRequest(Request $request): void
    {
    }

    protected function matchSignature(Request $request, ?string $signatureString): bool
    {
        $isSignatureMatched = false;
        if ($signatureString) {
            $signature = trim($signatureString, '"');
            $hmac = $this->getHmac($request);
            $isSignatureMatched = hash_equals($hmac, $signature);
        }
        return $isSignatureMatched;
    }

    protected function getHmac(Request $request): string
    {
        $hmacSignature = 'date: ' . $request->header('date') . "\n" . 'x-mod-nonce: ' . $request->header('x-mod-nonce');
        return urlencode(base64_encode(hash_hmac('sha256', $hmacSignature, $this->webhookClientSecret, true)));
    }

    protected function getSignatureStringFromHeader(?string $authorizationString): string
    {
        $signatureString = "";
        if ($authorizationString) {
            $signature = str_replace('Signature', '', $authorizationString);
            $signatureStringArray = explode(',', $signature);

            foreach ($signatureStringArray as $key => $val) {
                $entityArr = explode('=', str_replace(' ', '', $val));
                foreach ($entityArr as $k => $v) {
                    if ($entityArr[0] === "signature") {
                        $signatureString = $entityArr[1];
                        break;
                    }
                }
                if ($signatureString) {
                    break;
                }
            }
        }
        return $signatureString;
    }
}
