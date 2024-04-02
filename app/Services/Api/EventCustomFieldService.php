<?php

namespace App\Services\Api;

use App\Repositories\Event\EventCustomFieldRepository;
use App\Services\BaseService;

class EventCustomFieldService extends BaseService
{
    public function __construct()
    {
        $this->repo = new EventCustomFieldRepository();
    }

    public function store()
    {
        $attrs = [
            'event_id'  => $this->attributes['event_id'],
            'name'  => $this->attributes['name'],
            'value'  => $this->attributes['value'],
            'description'  => $this->attributes['description'] ?? null,
        ];

        if (!isset($this->attributes['id'])) {
            $attrMores = [
                'created_by'    => auth()->user()->id,
                'updated_by'    => auth()->user()->id,
                'event_id'      => $this->attributes['event_id']
            ];
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
                'updated_at'    => now(),
            ];
        }

        return $this->storeAs($attrs, $attrMores);
    }

    public function getList()
    {
        return $this->repo->getList(
            $this->getSearch(),
            $this->getFilters(),
            $this->attributes['orderBy'] ?? 'updated_at',
            $this->attributes['orderDesc'] ?? true,
            $this->attributes['limit'] ?? null,
            $this->attributes['pageSize'] ?? 50
        );
    }

    public function updateOrCreate($filters, $attrs)
    {
        return $this->repo->updateOrCreate($filters, $attrs);
    }

    public function removeByEventId($eventId)
    {
        return $this->repo->updateWithCondition(
            ['event_id' => $eventId],
            ['status' => $this->repo->getModel()::STATUS_DELETED]
        );
    }
}
