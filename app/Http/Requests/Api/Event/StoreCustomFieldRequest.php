<?php

namespace App\Http\Requests\Api\Event;

use App\Http\Requests\BaseFormRequest;

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
            '*.name'      => ['required', 'string'],
            '*.value'     => ['required', 'string'],
        ];

        return array_merge($rules, $ruleMores);
    }
}
