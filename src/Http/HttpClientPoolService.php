<?php

namespace Laravel\Infrastructure\Http;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class HttpClientPoolService extends HttpClientService
{

  public function get(): HttpClientResponse
  {
    $this->setContentTypeAppJson();
    $queryString = $this->queryString ? ["query" => $this->queryString] : [];
    return $this->call("get", $queryString);
  }

  public function callAll()
  {

    $responses = Http::pool(fn (Pool $pool) => [
      $pool->withHeaders()
    ]);
  }
}
