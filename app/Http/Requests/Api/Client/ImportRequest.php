<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\BaseFormRequest;

class ImportRequest extends BaseFormRequest
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
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:2048'],
        ];

        return array_merge($rules, $ruleMores);
    }
}
