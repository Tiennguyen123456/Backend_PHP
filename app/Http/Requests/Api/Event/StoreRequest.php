<?php

namespace App\Http\Requests\Api\Event;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use App\Models\Event;

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
            'code'              => ['required', 'string', 'max:50'],
            'name'              => ['required', 'string', 'max:255'],
            'start_time'        => ['required', 'date'],
            'end_time'          => ['required', 'date', 'after:start_time'],
            'company_id'        => ['required', 'numeric', $this->tableHasId('companies')],
            'status'            => ['required', 'string', 'max:50', Rule::in(array_keys(Event::getStatuesValid()))],
            'description'       => ['nullable', 'string', 'max:255'],
            'email_content'     => ['nullable', 'string'],
            'cards_content'     => ['nullable', 'string'],
        ];

        if (!empty($this->id)) {
            $ruleMores = [
                'id'    => ['numeric', $this->tableHasId('events')],
            ];
        }

        return array_merge($rules, $ruleMores);
    }
}
