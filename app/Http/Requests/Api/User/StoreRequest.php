<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

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
            'id'                => ['nullable', 'numeric', 'exists:users,id'],
            'role_id'           => ['nullable', 'numeric', $this->tableHasId('roles')],
            'name'              => ['required', 'string', 'max:255'],
            'username'          => ['required', 'string', 'max:255', Rule::unique('users')],
            'email'             => ['required', 'string', 'max:255', Rule::unique('users')],
            'status'            => ['required', 'string', 'max:50', Rule::in(array_keys(User::getStatuesValid()))],
            'company_id'        => ['nullable', 'numeric', $this->tableHasId('companies')],
        ];

        if ($this->id) {
            $ruleMores = [
                'username'    => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->username, 'username')],
                'email'       => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->email, 'email')],
            ];
        }

        return array_merge($rules, $ruleMores);
    }
}
