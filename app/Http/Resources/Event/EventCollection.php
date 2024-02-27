<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\BaseCollection;
use Illuminate\Http\Request;

class EventCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'count'         => $this->collection->count(),
            'collection'    => parent::toArray($request),
            'pagination'    => $this->getPaginateMeta(),
        ];
    }
}
