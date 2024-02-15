<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\BaseFormRequest;

class SendMailResetPasswordRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'     => ['required', 'email', 'max:255'],
        ];
    }
}
