<?php

namespace Laravel\Infrastructure\PubSubs;

use Aws\Sqs\SqsClient;
use Illuminate\Config\Repository;

trait ProvidesSqs
{
  public function sendMessageToSqs(string $message)
  {
    $client = $this->makeClient();

    // $client->createQueue(['QueueName' => 'test-queue']);

    // $client->purgeQueue(['QueueUrl' => '']);

    $client->sendMessage([
      'QueueUrl' => 'https://sqs.eu-west-2.amazonaws.com/710768145931/dev-accounting-integration-default',
      'MessageBody' => $message,
    ]);

    // $this->queueConfiguration();
  }

  //   AWS_ACCESS_KEY_ID=AKIA2K7IVGYFVRQ27UVU
  // AWS_SECRET_ACCESS_KEY=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

  protected function makeClient(): SqsClient
  {
    $client = new SqsClient([
      'endpoint' => 'https://sqs.eu-west-2.amazonaws.com/710768145931/dev-accounting-integration-default',
      'version' => 'latest',
      'region' => 'eu-west-2',
      'credentials' => [
        'key' => 'AKIA2K7IVGYFYC7QZSXJ',
        'secret' => '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',
      ]
    ]);

    return $client;
  }

  protected function queueConfiguration()
  {
    // $config = app()->make(Repository::class);

    // $config->set('queue.default', 'sns');
    // $config->set('queue.connections.sns.endpoint', 'https://sqs.eu-west-2.amazonaws.com/710768145931/dev-accounting-integration-default');
    // $config->set('queue.connections.sns.key', 'foo');
    // $config->set('queue.connections.sns.secret', 'bar');
    // $config->set('queue.connections.sns.queue', 'https://sqs.eu-west-2.amazonaws.com/710768145931/dev-accounting-integration-default');
    // $config->set('queue.connections.sns.region', 'eu-west-2');
  }
}
