<?php

namespace Laravel\Infrastructure\Rules;

class FileUploadRule extends BaseRule
{
    public function __construct($types = "doc,docx,pdf,png,jpg,jpeg,gif,heic", $size = 10240)
    {
        $this->types = $types;
        $this->size = $size;  // Default 10MB.
    }

    public function passes($attribute, $value): bool
    {
        $rules = ["bail", "nullable", "file", "mimes:" . $this->types, "max:" . $this->size];
        return $this->validate($value, $rules, $attribute);
    }
}
