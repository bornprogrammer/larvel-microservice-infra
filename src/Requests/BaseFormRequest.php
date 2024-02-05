<?php

namespace Laravel\Infrastructure\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function defineUUIdValidation(): array
    {
        return ["required", "uuid"];
    }
}
