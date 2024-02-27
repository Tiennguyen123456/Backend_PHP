<?php

namespace App\Http\Requests\Api\Client;

use App\Models\Client;
use Illuminate\Validation\Rule;
use App\Rules\UniquePhoneInEvent;
use App\Http\Requests\BaseFormRequest;

class StoreRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $eventId = $this->route('id');

        $ruleMores = [];

        $rules = [
            'phone'     => ['required', 'string', 'max:20', new UniquePhoneInEvent($eventId)],
            'fullname'   => ['required', 'string', 'max:150'],
            'email'      => ['nullable', 'email', 'max:100'],
            'address'    => ['nullable', 'string', 'max:255'],
            'status'     => ['required', 'string', 'max:50', Rule::in(array_keys(Client::getStatuesValid()))],
            'is_checkin' => ['nullable', 'boolean'],
            'group'      => ['nullable', 'string', 'max:100'],
            'type'       => ['nullable', 'string', 'max:50'],
        ];

        if ($this->id) {
            $ruleMores = [
                'id'        => ['integer', Rule::exists('clients')->where('event_id', $eventId)],
                'phone'     => ['string', 'max:20', new UniquePhoneInEvent($eventId, $this->id)],
            ];
        }

        return array_merge($rules, $ruleMores);
    }
}
