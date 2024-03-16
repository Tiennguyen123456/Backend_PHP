<?php

namespace App\Http\Requests\Api\Event;

use App\Models\Event;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreCustomFieldRequest extends BaseFormRequest
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
            'name'        => ['required', 'string', Rule::notIn(array_keys(Event::MAIN_FIELDS)), $this->uniqueCustomField($this->event_id)],
            'value'       => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'event_id'    => ['numeric', $this->tableHasId('events')],
        ];

        if (!empty($this->id)) {
            $ruleMores = [
                'name'  => ['required', 'string', Rule::notIn(array_keys(Event::MAIN_FIELDS)), $this->uniqueCustomField($this->event_id, $this->id)],
                'id'    => ['numeric', $this->tableHasId('event_custom_fields')],
            ];
        }

        return array_merge($rules, $ruleMores);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'event_id' => (int) $this->route('id')
        ]);
    }
}
