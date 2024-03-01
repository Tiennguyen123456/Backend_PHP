<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Request;
use App\Http\Resources\BaseCollection;

class EventCollection extends BaseCollection
{
    protected $hiddenColumns = [
        'email_content',
        'cards_content',
        'main_fields',
        'custom_fields',
    ];
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'count'         => $this->collection->count(),
            'collection'    => $this->finalizeCollection(),
            'pagination'    => $this->getPaginateMeta(),
        ];
    }

    public function finalizeCollection()
    {
        return $this->collection->map(function ($event) {
            $data = $event->resource->toArray();

            $data['company'] = $event->company()->first(['id', 'name']);

            foreach ($this->hiddenColumns as $column) {
                unset($data[$column]);
            }
            return $data;
        });
    }
}
