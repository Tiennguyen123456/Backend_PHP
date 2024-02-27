<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class EventResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->attrOnly = [

        ];

        $this->attrMores = [
            'email_content' => $this->email_content,
            'cards_content' => $this->cards_content,
        ];

        $this->attrExcepts = [

        ];

        return $this->finalizeResult($request);
    }
}
