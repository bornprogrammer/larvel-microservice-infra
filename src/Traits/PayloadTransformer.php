<?php

namespace Laravel\Infrastructure\Traits;

use Illuminate\Contracts\Validation\Validator;

trait PayloadTransformer
{
    protected function getValidatorInstance(): Validator
    {
        $this->merge($this->transform());
        return parent::getValidatorInstance();
    }

    protected abstract function transform(): array;
}
