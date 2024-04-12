<?php

namespace App\Http\Requests\Api\Client;

use App\Models\Client;
use Illuminate\Validation\Rule;
use App\Rules\UniquePhoneInEvent;
use App\Services\Api\PostService;
use App\Http\Requests\BaseFormRequest;

class RegisterRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_id'  => ['required', 'integer', $this->tableHasId('events')],
            'email'     => ['nullable', 'email', 'max:100'],
            'phone'     => ['required', 'string', 'max:20', new UniquePhoneInEvent($this->event_id)],
            'fullname'  => ['required', 'string', 'max:150'],
            'address'   => ['nullable', 'string', 'max:255'],
            'type'      => ['nullable', 'string', 'max:50'],

            // 'status'     => ['required', 'string', 'max:50', Rule::in(array_keys(Client::getStatuesValid()))],
            // 'is_checkin' => ['nullable', 'boolean'],
            // 'group'      => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $event_id = null;
        $uniqueId = $this->unique_id;

        if (!blank($uniqueId)) {
            $postService = app(PostService::class);
            $post = $postService->findByUniqueId($uniqueId);

            if ($post)
                $event_id = $post->event_id;
        }

        $this->merge([
            'event_id' => (int) $event_id,
        ]);
    }
}
