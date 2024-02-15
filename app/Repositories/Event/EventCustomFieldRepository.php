<?php

namespace App\Repositories\Event;

use App\Repositories\Repository;

class EventCustomFieldRepository extends Repository implements EventCustomFieldRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\EventCustomField::class;
    }

    public function getListByEventId($eventId)
    {
        return $this->model->where('event_id', $eventId)->get();
    }

    public function getList($searches = [], $filters = [], $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0, $paginate = 50)
    {
        $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED);

        if ($orderByDesc) {
            $query = $query->orderBy($orderByColumn, 'desc');
        } else {
            $query = $query->orderBy($orderByColumn, 'asc');
        }

        $query = $this->addSearchQuery($query, $searches);

        /* FILTER */
        if (count($filters)) {
            if (isset($filters['event_id'])) {
                $query = $query->where('event_id', $filters['event_id']);
            }
        }

        if ($limit > 0) {
            $query = $query->limit($limit);
        }

        if ($paginate > 0) {
            return $query->paginate($paginate);
        }

        return $query->get();
    }

    public function updateOrCreate($filters, $attrs)
    {
        return $this->model->updateOrCreate($filters, $attrs);
    }
}
