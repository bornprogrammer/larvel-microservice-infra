<?php

namespace Laravel\Infrastructure\PubSubs;

class JobMap
{
  private $map;

  public function __construct(array $map)
  {
    $this->map = $map;
  }

  public function fromTopic(string $topic): string
  {
    // $job = array_search($topic, $this->map);
    $job = $this->getMappedPubSubscriber($topic);

    if (!$job) {
      throw new \Exception("Topic $topic is not mapped to any Job");
    }
    return $job;
  }

  protected function getMappedPubSubscriber(string $topic)
  {
    $topicName = substr($topic, strrpos($topic, ":") + 1);
    return $this->map[$topicName];
  }
}
