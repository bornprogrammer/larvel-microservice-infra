<?php

namespace Laravel\Infrastructure\PubSubs;

use Illuminate\Queue\Connectors\SqsConnector;
use Illuminate\Support\Arr;
use Aws\Sqs\SqsClient;

class SnsConnector extends SqsConnector
{
  /**
   * Establish a queue connection.
   *
   * @param array $config
   * @return \Illuminate\Contracts\Queue\Queue
   */
  public function connect(array $config)
  {
    $config = $this->getDefaultConfiguration($config);

    if ($config['key'] && $config['secret']) {
      $config['credentials'] = Arr::only($config, ['key', 'secret']);
    }

    return new SnsQueue(
      new SqsClient($config),
      $config['queue'],
      Arr::get($config, 'prefix', ''),
      Arr::get(config("queue"), 'pub-sub-mappers', [])
    );
  }
}
