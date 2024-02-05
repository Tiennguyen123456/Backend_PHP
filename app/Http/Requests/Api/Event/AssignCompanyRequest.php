<?php

namespace App\Http\Requests\Api\Event;

use App\Http\Requests\BaseFormRequest;

class AssignCompanyRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'            => ['required', 'numeric', $this->tableHasId('events')],
            'company_id'    => ['required', 'numeric', $this->tableHasId('companies')],
        ];
    }
}
