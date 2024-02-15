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
            'name'      => $this->attributes['name'],
            'value'     => $this->attributes['value'],
        ];

        if (!isset($this->attributes['id'])) {
            $attrMores = [
                'created_by'            => auth()->user()->id,
                'updated_by'            => auth()->user()->id,
            ];
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
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
}
