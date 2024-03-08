<?php

namespace App\Http\Requests\Api\Event;

use App\Models\Event;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreCustomFieldRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ruleMores = [];

        $rules = [
            '*.name'        => ['required', 'string', Rule::notIn(array_keys(Event::MAIN_FIELDS))],
            '*.value'       => ['required', 'string'],
            '*.description' => ['nullable', 'string'],
        ];

        return array_merge($rules, $ruleMores);
    }
}
