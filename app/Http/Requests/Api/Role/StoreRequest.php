<?php

namespace App\Http\Requests\Api\Role;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseFormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($this->id)],
            'guard_name' => ['nullable', 'string', 'max:255']
        ];

        return array_merge($rules, $ruleMores);
    }
}
