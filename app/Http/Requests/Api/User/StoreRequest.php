<?php

namespace App\Http\Requests\Api\User;

use App\Models\User;
use Illuminate\Validation\Rule;
use App\Traits\SanitizedRequest;
use App\Http\Requests\BaseFormRequest;

class StoreRequest extends BaseFormRequest
{
    use SanitizedRequest;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ruleMores = [];
        $rules = [
            'id'                => ['nullable', 'numeric', $this->tableHasId('users')],
            'role_id'           => ['nullable', 'numeric', $this->tableHasId('roles')],
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', Rule::unique('users')],
            'password'          => ['nullable', 'string', 'min:8', 'max:255'],
            'status'            => ['required', 'string', 'max:50', Rule::in(array_keys(User::getStatuesValid()))],
            'company_id'        => ['nullable', 'numeric', $this->tableHasId('companies')],
        ];

        if ($this->id) {
            $ruleMores = [
                'email'       => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->id)],
            ];
        }

        return array_merge($rules, $ruleMores);
    }
}
