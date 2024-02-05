<?php

namespace Laravel\Infrastructure\PubSubs;

class SNSHelper
{
  public static function routeExists(array $routes, array $body): string|bool
  {
    // $body = json_decode($message['Body'], true);
    $param = "TopicArn";
    if (isset($body[$param])) {
      $topicArn = $body[$param];
      $topicArn = substr($topicArn, strrpos($topicArn, ":") + 1);
      $topicArn = substr($topicArn, 0, strrpos($topicArn, "_"));
      if (array_key_exists($topicArn, $routes)) {
        return $routes[$topicArn];
      }
    }
    return false;
  }
}
