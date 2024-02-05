<?php

namespace Laravel\Infrastructure\PubSubProcessors;

abstract class BasePubSubProcessor
{
  public abstract function process($params);
}
