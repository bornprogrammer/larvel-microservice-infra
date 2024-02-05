<?php

namespace Laravel\Infrastructure\DTOS;

use Illuminate\Http\Request;

class MultipleUUIDDTO extends BaseDTO
{
    public readonly ?array $ids;

    public static function fromRequest(Request $request): self
    {
        return new self([
            "ids" => isset($request["id"]) ? explode(",", $request["id"]) : null,
        ]);
    }
}
