<?php

namespace Laravel\Infrastructure\Rules;

use Laravel\Infrastructure\Rules\BaseRule;
use Laravel\Infrastructure\Rules\UUIDRule;

class MultipleUUIDArrayRule extends BaseRule
{
    /**
     */
    public function passes($attribute, $value): bool
    {
        $isValidationPassed = true;
        if (is_array($value) && count($value) > 0) {
            $uuIds = $value;
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
