<?php

namespace Laravel\Infrastructure\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\Validator;
use Laravel\Infrastructure\Log\Logger;

class BaseRule implements ImplicitRule
{
    protected array|string $errMessage;

    protected mixed $validator;

    protected function validate(mixed $value, array $rules, string $name): bool
    {
        $this->makeValidator($name, $value, $rules);
        return $this->validator->passes();
    }

    protected function makeValidator($name, $value, $rules)
    {
        $this->validator = Validator::make([$name => $value], [$name => $rules]);
    }

    public function passes($attribute, $value)
    {
        // TODO: Implement passes() method.
    }

    public function message(): array|string
    {
        //        $this->errMessage = "";
        $errors = $this->validator?->errors();
        Logger::info("errors");
        Logger::info($errors);
        if ($errors->any()) {
            $this->errMessage = $errors->all();
        }
        return $this->errMessage;
    }
}
