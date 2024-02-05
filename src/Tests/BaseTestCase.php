<?php

namespace Laravel\Infrastructure\Tests;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;

abstract class BaseTestCase extends TestCase
{
  protected string $path;
  protected ?array $payload;

  protected string $token;
  public function __construct(string $path, ?array $payload = [], $name = null, array $data = array(), $dataName = '')
  {
    $this->path = $path;
    $this->payload = $payload;
    $this->token = env("token");
    parent::__construct($name, $data, $dataName);
  }

  protected function callPost(): TestResponse
  {
    return $this->postJson($this->path, $this->payload, ['Authorization' => $this->token]);
  }

  protected function callGet(): TestResponse
  {
    return $this->getJson($this->path, ['Authorization' => $this->token]);
  }

  protected function callDelete(): TestResponse
  {
    return $this->deleteJson($this->path, $this->payload, ['Authorization' => $this->token]);
  }

  protected function callPut(): TestResponse
  {
    return $this->putJson($this->path, $this->payload, ['Authorization' => $this->token]);
  }
}
