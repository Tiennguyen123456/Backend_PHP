<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\BaseResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventResource extends BaseResource
{
    public function eventModel()
    {
        return new Event();
    }

    public function toArray(Request $request): array
    {
        $arCustomField = $this->custom_fields()->get(['id', 'name', 'value', 'description']);

        $arMainField = [];

        foreach ($this->eventModel()::MAIN_FIELDS as $key => $value) {
            $arMainField[] = [
                'name' => $key,
                'description' => $value,
            ];
        }

        $this->attrMores = [
            'email_content' => $this->email_content,
            'cards_content' => $this->cards_content,
            'main_fields' => $arMainField,
            'custom_fields' => $arCustomField,
            'company' => $this->company()->first(['id', 'name']),
        ];

        return $this->finalizeResult($request);
    }
}
