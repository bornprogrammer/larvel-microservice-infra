<?php

namespace Laravel\Infrastructure\PubSubs;

use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\SqsJob;
use Illuminate\Queue\CallQueuedHandler;
use Laravel\Infrastructure\Log\SessionLogger;

class SnsJob extends SqsJob
{
  public function __construct(Container $container, SqsClient $sqs, array $job, string $connectionName, string $queue, array $routes = [])
  {
    parent::__construct($container, $sqs, $job, $connectionName, $queue);
    $this->job = $this->resolveSnsSubscription($job, $routes);
  }

  protected function resolveSnsSubscription(array $job, array $routes)
  {
    $body = json_decode($job['Body'], true);

    $commandName = null;

    // available parameters to route your jobs by
    $possibleRouteParams = ['TopicArn'];

    $commandName = SNSHelper::routeExists($routes, $body);
    // $commandName = $commandName !== false ? $commandName : null;

    if ($commandName !== false) {
      // If there is a command available, we will resolve the job instance for it from
      // the service container, passing in the subject and the payload of the
      // notification.

      $command = $this->makeCommand($commandName, $body);

      // The instance for the job will then be serialized and the body of
      // the job is reconstructed.

      $job['Body'] = json_encode([
        'uuid' => $body['MessageId'],
        'displayName' => $commandName,
        'job' => CallQueuedHandler::class . '@call',
        'data' => compact('commandName', 'command'),
      ]);
    }

    return $job;
  }

  /**
   * Make the serialized command.
   *
   * @param string $commandName
   * @param array  $body
   * @return string
   */
  protected function makeCommand($commandName, $body)
  {
    SessionLogger::start(__METHOD__, ['command_name' => $commandName]);
    $payload = json_decode($body['Message'], true);

    $data = [
      'subject' => (isset($body['Subject'])) ? $body['Subject'] : '',
      'payload' => $payload
    ];

    $instance = $this->container->make($commandName, $data);
    SessionLogger::end(__METHOD__, ['instance' => $instance]);
    return serialize($instance);
  }



  /**
   * Get the underlying raw SQS job.
   *
   * @return array
   */
  public function getSqsSnsJob()
  {
    return $this->job;
  }
}
