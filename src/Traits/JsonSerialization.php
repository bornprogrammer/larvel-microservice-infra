<?php

namespace Laravel\Infrastructure\Traits;

trait JsonSerialization
{
    public function __toString(): string
    {
        return json_encode($this);
    }
}
