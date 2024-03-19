<?php

namespace App\Http\Requests\Api\Event;

use App\Http\Requests\BaseFormRequest;

class QrCheckinRequest extends BaseFormRequest
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
            'code'  => ['required', 'string', 'max:100'],
        ];

        return array_merge($rules, $ruleMores);
    }
}
