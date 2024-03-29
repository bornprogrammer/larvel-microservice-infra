<?php

namespace Laravel\Infrastructure\PubSubs;

use Illuminate\Support\ServiceProvider;
use Laravel\Infrastructure\PubSubs\SnsConnector;

class SNSQueueServiceProvider extends ServiceProvider
{
  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    // nothing to register
  }

  /**
   * Bootstraps the 'queue' with a new connector 'sqs-sns'
   *
   * @return void
   */
  public function boot()
  {
    $this->app['queue']->extend('sns', function () {
      return new SnsConnector;
    });
  }
}
