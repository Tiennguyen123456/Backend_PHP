<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\BaseFormRequest;

class ListRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'orderBy'           => ['nullable', 'string', $this->tableHasColumn('users')],
            'orderDesc'         => ['nullable', 'boolean'],
            'paginate'          => ['nullable', 'numeric', 'min:5'],
            'limit'             => ['nullable', 'numeric', 'min:1'],
            'filters'           => ['nullable', 'array'],
            'filters.status'    => ['nullable', 'string', 'max:50'],
            'filters.type'      => ['nullable', 'string', 'max:50'],
            'filters.gate'      => ['nullable', 'string', 'max:50'],
            'filters.start_time' => ['nullable', 'date'],
            'filters.end_time'  => ['nullable', 'date'],
            'filters.role_id'   => ['nullable', 'numeric'],
            'search'            => ['nullable', 'array'],
            'search.*'          => ['nullable'],
        ];
    }
}
