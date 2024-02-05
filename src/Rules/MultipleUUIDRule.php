<?php

namespace Laravel\Infrastructure\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MultipleUUIDRule extends BaseRule
{
    /**
     */
    public function passes($attribute, $value): bool
    {
        $isValidationPassed = true;
        if ($value && strlen($value) > 0) {
            $uuIds = explode(",", $value);
            foreach ($uuIds as $uuId) {
                $this->makeValidator($attribute, $uuId, [new UUIDRule()]);
                $isFailed = $this->validator->fails();
                if ($isFailed) {
                    $isValidationPassed = false;
                    break;
                }
            }
        }
        return $isValidationPassed;
    }
}
