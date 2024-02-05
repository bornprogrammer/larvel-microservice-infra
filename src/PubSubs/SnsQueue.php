<?php

namespace Laravel\Infrastructure\PubSubs;

use Aws\Sqs\SqsClient;
use Illuminate\Queue\SqsQueue;
use Laravel\Infrastructure\Log\Logger;
use Laravel\Infrastructure\Log\SessionLogger;

class SnsQueue extends SqsQueue
{
  protected $routes;

  public function __construct(SqsClient $sqs, string $default, $prefix = '', $routes = [])
  {
    parent::__construct($sqs, $default, $prefix);
    $this->routes = $routes;
  }

  public function pop($queue = null)
  {
    $queue = $this->getQueue($queue);

    SnsWorkerLogger::start();

    $response = $this->sqs->receiveMessage([
      'QueueUrl' => $queue,
      'AttributeNames' => ['ApproximateReceiveCount'],
    ]);

    if (is_array($response['Messages']) && count($response['Messages']) > 0) {
      $body = json_decode($response['Messages'][0]['Body'], true);
      SessionLogger::start(__METHOD__ . " message-received", $body);
      if (SNSHelper::routeExists($this->routes, $body) || $this->classExists($response['Messages'][0])) {
        return new SnsJob(
          $this->container,
          $this->sqs,
          $response['Messages'][0],
          $this->connectionName,
          $queue,
          $this->routes
        );
      } else {
        // remove unwanted messages from topics with multiple messages
        $this->sqs->deleteMessage([
          'QueueUrl' => $queue, // REQUIRED
          'ReceiptHandle' => $response['Messages'][0]['ReceiptHandle'] // REQUIRED
        ]);
      }
    }
  }


  /**
   * Check if the job class
   * you're trying to trigger exists.
   *
   * @param array $message
   * @return bool
   */
  protected function classExists(array $message)
  {
    $body = json_decode($message['Body'], true);

    return isset($body['data']['commandName']) && class_exists($body['data']['commandName']);
  }
}
