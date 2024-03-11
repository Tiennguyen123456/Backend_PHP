<?php

namespace App\Http\Requests\Api\Campaign;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use App\Models\Campaign;

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
            'name'              => ['required', 'string', 'max:100'],
            'company_id'        => ['required', 'numeric', $this->tableHasId('companies')],
            'event_id'          => ['required', 'numeric', $this->tableHasId('events')],
            'run_time'          => ['nullable', 'date', 'after:now'],
            'filter_client'     => ['nullable', 'array'],
            'status'            => ['required', 'string', 'max:50', Rule::in(array_keys(Campaign::getStatuesValid()))],
            // 'mail_content'      => ['required', 'string'],
            'mail_subject'      => ['required', 'string', 'max:100'],
            'sender_email'      => ['nullable', 'string', 'max:100'],
            'sender_name'       => ['nullable', 'string', 'max:100'],
            'description'       => ['nullable', 'string', 'max:255'],
        ];

        if ($this->id) {
            $ruleMores = [
                'id'    => ['numeric', $this->tableHasId('campaigns')],
            ];
        }

        return array_merge($rules, $ruleMores);
    }
}
