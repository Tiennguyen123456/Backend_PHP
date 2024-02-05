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
            'id'                => ['nullable', 'numeric'],
            'is_default'        => ['nullable', 'boolean'],
            'description'       => ['nullable', 'string', 'max:255'],
            'location'          => ['nullable', 'string', 'max:255'],
            // 'contact_name'      => ['nullable', 'string', 'max:255'],
            // 'contact_email'     => ['nullable', 'string', 'max:255'],
            // 'contact_phone'     => ['nullable', 'string', 'max:255'],
            // 'note'              => ['nullable', 'string', 'max:255'],
            // 'encrypt_file_link' => ['nullable', 'boolean'],
            'status'            => ['nullable', 'string', 'max:50', Rule::in(array_keys(Event::getStatuesValid()))],
        ];

        if (empty($this->id)) {
            $ruleMores = [
                'company_id'    => ['required', 'numeric', $this->tableHasId('companies')],
                'code'          => ['required', 'string', 'max:200'],
                'name'          => ['required', 'string', 'max:255'],
                'start_time'     => ['required', 'date'],
                'end_time'      => ['required', 'date'],
            ];
        }

        return array_merge($rules, $ruleMores);
    }
}
