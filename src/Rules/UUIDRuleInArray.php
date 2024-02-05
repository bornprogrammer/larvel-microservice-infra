<?php

namespace Laravel\Infrastructure\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\Validator;

class UUIDRuleInArray extends BaseRule
{
    public function __construct(private string $fieldName, private string $idNameInsideField = "id")
    {
    }

    public function passes($attribute, $value): bool
    {
        $index = explode('.', $attribute)[1];
        $prefix = request()->input("{$this->fieldName}.{$index}.{$this->idNameInsideField}");
        return $this->validate($value, ["required", "uuid"], $prefix);
    }
}
