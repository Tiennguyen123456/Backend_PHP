<?php

namespace App\Http\Requests\Api\Post;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
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
            'slug'              => ['required', 'string', Rule::unique('posts')],
            'title'             => ['nullable', 'string'],
            'subtitle'          => ['nullable', 'string'],
            'content'           => ['nullable', 'string'],
            'background_img'    => ['nullable', 'file', 'mimes:jpeg,jpg,png', 'max:2048'],
            'form_enable'       => ['required', 'boolean'],
            'form_title'        => ['nullable', 'string'],
            'form_content'      => ['nullable', 'string'],
            'form_input'        => ['nullable', 'array'],
        ];

        if (!empty($this->id)) {
            $ruleMores = [
                'id'    => ['numeric', $this->tableHasId('posts')],
                // 'slug' => ['required', 'string', Rule::unique('posts')->ignore($this->id)->where(function ($query) {
                //     return $query->where('status', '!=', Post::STATUS_DELETED);
                // })],
                'slug' => ['required', 'string', Rule::unique('posts', 'slug')->ignore($this->id)],
            ];
        }

        return array_merge($rules, $ruleMores);
    }
}
