<?php

namespace App\Http\Requests\Api\Campaign;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use App\Models\Campaign;

class UpdateRequest extends BaseFormRequest
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
            'id'        => ['required', 'numeric', $this->tableHasId('campaigns')],
            'action'    => ['required', 'string', 'max:50', Rule::in(array_keys(Campaign::getActions()))],
        ];

        return array_merge($rules, $ruleMores);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->id,
        ]);
    }
}
