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
        $eventId = $this->attributes['event_id'];
        $arData = $this->attributes['data'];

        foreach ($arData as $field) {
            $this->updateOrCreate(
                [
                    'event_id' => $eventId,
                    'name' => $field['name']
                ],
                [
                    'event_id'      => $eventId,
                    'name'          => $field['name'],
                    'value'         => $field['value'],
                    'description'   => $field['description'],
                    'created_by'    => auth()->user()->id,
                    'updated_by'    => auth()->user()->id,
                ]
            );
        }

        return true;
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
}
