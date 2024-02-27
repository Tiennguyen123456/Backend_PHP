<?php

namespace App\Rules;

use App\Services\Api\ClientService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhoneInEvent implements ValidationRule
{
    protected $eventId;

    protected $ignoreId = null;

    private $clientService;

    public function __construct($eventId, $ignoreId = null)
    {
        $this->eventId = $eventId;
        $this->ignoreId = $ignoreId;
        $this->clientService = new ClientService();
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->clientService->attributes['filters']['event_id'] = $this->eventId;
        $this->clientService->attributes['filters']['phone'] = $value;
        $this->clientService->attributes['limit'] = 1;

        $result = $this->clientService->getAll();
        if (!blank($result)) {
            $record = $result->first();

            if ($this->ignoreId) {
                if ($record->id != $this->ignoreId) {
                    $fail(trans('validation.unique', ['attribute' => $attribute]));
                }
            } else {
                $fail(trans('validation.unique', ['attribute' => $attribute]));
            }
        }
    }
}
