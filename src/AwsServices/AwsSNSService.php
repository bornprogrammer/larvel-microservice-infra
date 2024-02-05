<?php

namespace Laravel\Infrastructure\AwsServices;

use Illuminate\Support\Str;
use Laravel\Infrastructure\Exceptions\PublishTopicFailedException;
use Laravel\Infrastructure\Helpers\EnvironmentHelper;
use Laravel\Infrastructure\Log\SessionLogger;

class AwsSNSService extends AwsCommonSNSService
{
    /**
     * that is going to publish the sns to Topic created specially for Kloo PubSub
     *
     * @param string $topicName
     * @param array $message
     * @return void
     */
    public function publish(string $topicName, array $message): ?array
    {

        SessionLogger::start(__METHOD__, ['topic' => $topicName, 'message' => $message]);
        $message['token'] = request()->header("Authorization");
        $message['unique_message_id'] = Str::uuid()->toString();
        $topicName = $this->buildTopicWithEnv($topicName);
        $result = $this->process($topicName, $message);
        SessionLogger::end(__METHOD__, ['topic' => $topicName, 'message' => $message]);
        return $result?->toArray();
    }

    public function handleSDKException(\Throwable $throwable, ?array $params = null): void
    {
        throw new PublishTopicFailedException($throwable, $params);
    }

    public function publishOrHttp(string $topicName, array $message, $callback)
    {
        $result = null;
        $isSNSEnabled = config("queue.connections.sns")["is_enabled"] ?? false;
        if ($isSNSEnabled && (EnvironmentHelper::isLocal() || EnvironmentHelper::isDevelopment())) {
            $result = $this->publish($topicName, $message);
        } else {
            $result = call_user_func($callback, $topicName, $message);
        }
        return $result;
    }
}
