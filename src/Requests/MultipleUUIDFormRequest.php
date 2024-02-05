<?php

namespace Laravel\Infrastructure\Requests;

use Laravel\Infrastructure\Rules\MultipleUUIDRule;
use Laravel\Infrastructure\Traits\MergeRequestParamForValidation;

class MultipleUUIDFormRequest extends BaseFormRequest
{
    use MergeRequestParamForValidation;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "id" => [new MultipleUUIDRule()],
        ];
    }
}
