<?php

namespace App\Http\Requests\Api\Client;

use App\Models\Client;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $eventId = $this->route('id');
        $clientId = $this->route('clientId');

        $ruleMores = [];

        $rules = [
            'phone'      => ['required', 'string', 'max:20',
                                Rule::unique('clients')
                                    ->where(function ($query) use ($eventId, $clientId) {
                                        return $query->where('event_id', $eventId)->where('id', '!=', $clientId);
                                    })
                            ],
            'email'      => ['nullable', 'email', 'max:100'],
            'fullname'   => ['nullable', 'string', 'max:150'],
            'address'    => ['nullable', 'string', 'max:255'],
            'status'     => ['required', 'string', 'max:50', Rule::in(array_keys(Client::getStatuesValid()))],
            'is_checkin' => ['nullable', 'boolean'],
        ];

        return array_merge($rules, $ruleMores);
    }
}
