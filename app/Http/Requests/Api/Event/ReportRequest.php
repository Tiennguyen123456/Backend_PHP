<?php

namespace App\Http\Requests\Api\Event;

use App\Http\Requests\BaseFormRequest;

class ReportRequest extends BaseFormRequest
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
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date', 'after:from'],
        ];

        return array_merge($rules, $ruleMores);
    }
}
