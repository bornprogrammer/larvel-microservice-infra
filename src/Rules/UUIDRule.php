<?php

namespace Laravel\Infrastructure\Rules;

class UUIDRule extends BaseRule
{
    public function passes($attribute, $value): bool
    {
        return $this->validate($value, ["required", "uuid"], $attribute);
    }
}
