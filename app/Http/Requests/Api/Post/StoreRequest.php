<?php

namespace App\Http\Requests\Api\Post;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Requests\BaseFormRequest;
use App\Models\Post;

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
            'company_id'        => ['required', 'numeric', $this->tableHasId('companies')],
            'event_id'          => ['required', 'numeric', $this->tableHasId('events')],
            'name'              => ['required', 'string'],
            'slug'              => ['required', 'string', Rule::unique('posts', 'slug')],
            'title'             => ['nullable', 'string'],
            'subtitle'          => ['nullable', 'string'],
            'content'           => ['nullable', 'string'],
            'background_img'    => ['nullable', 'file', 'mimes:jpeg,jpg,png', 'max:2048'],
            'form_enable'       => ['nullable', 'boolean'],
            'form_title'        => ['nullable', 'string'],
            'form_content'      => ['nullable', 'string'],
            'form_input'        => ['nullable', 'array'],
            'status'            => ['required', 'string', 'max:50', Rule::in(array_keys(Post::getStatuesValid()))],
        ];

        if (!empty($this->id)) {
            $ruleMores = [
                'id'    => ['numeric', $this->tableHasId('posts')],
                'slug' => ['required', 'string', Rule::unique('posts', 'slug')->ignore($this->id)],
            ];
        }

        return array_merge($rules, $ruleMores);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string to boolean

        $this->merge([
            'form_enable' => filter_var($this->form_enable , FILTER_VALIDATE_BOOLEAN),
            'slug' => Str::slug($this->slug),
        ]);
    }
}
