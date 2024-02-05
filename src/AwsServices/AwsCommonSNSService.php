<?php

namespace Laravel\Infrastructure\AwsServices;

use Aws\Sns\SnsClient;
use Laravel\Infrastructure\Constants\EnvironmentConstants;
use Laravel\Infrastructure\Helpers\EnvironmentHelper;
use Laravel\Infrastructure\Log\SessionLogger;

class AwsCommonSNSService extends BaseAWSService
{
    protected static $awsClientType = SnsClient::class;

    public function __construct()
    {
        parent::__construct("queue.connections.sns");
    }

    protected function execute(string|array ...$params)
    {
        SessionLogger::start(__METHOD__);
        $params = current($params);
        $topicArn = $params[0];
        $message = $params[1];
        $result = $this->awsClient->publish([
            "Message" => json_encode($message),
            'TopicArn' => $topicArn,
            'Subject' => 'testing'
        ]);
        SessionLogger::end(__METHOD__);
        return $result;
    }

    public function buildTopic(string $topicName)
    {
        $region = $this->sdkConfiguration['region'];
        $prefix = $this->sdkConfiguration['prefix'];
        $topicName = "arn:aws:sns:" . $region . ":" . $prefix . ":" . $topicName;
        return $topicName;
    }

    public function buildTopicWithEnv(string $topicName)
    {
        $topic = $this->buildTopic($topicName);
        $envName = EnvironmentHelper::getEnvironmentName();
        $envName = EnvironmentHelper::isLocal() ? EnvironmentConstants::ENVIRONMENT_DEVELOPMENT : $envName;
        $topicName = $topic . "_" . $envName;
        return $topicName;
    }
}
