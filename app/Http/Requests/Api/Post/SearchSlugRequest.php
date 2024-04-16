<?php

namespace App\Http\Requests\Api\Post;

use App\Http\Requests\BaseFormRequest;

class SearchSlugRequest extends BaseFormRequest
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
            'slug'  => ['required', 'string'],
        ];

        return array_merge($rules, $ruleMores);
    }
}
