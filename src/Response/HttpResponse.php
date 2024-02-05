<?php

namespace Laravel\Infrastructure\Response;

class HttpResponse
{
    public ?array $result;
    public string $message = "";
    public int $statusCode;

    public function __construct(?array $data, string $message, int $statusCode)
    {
        $this->result = $data;
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
