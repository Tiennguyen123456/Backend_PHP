<?php
namespace App\Repositories\Event;

use App\Repositories\Repository;

class EventRepository extends Repository implements EventRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Event::class;
    }

    public function reportByStatus($filters = [])
    {
        $query = $this->model;

        $query = $this->addFilterCompanyQuery($query);

        if (!blank($filters)) {
            if (isset($filters['from'])) {
                $query = $query->where('created_at', '>=', $filters['from']);
            }
            if (isset($filters['from'])) {
                $query = $query->where('created_at', '<=', $filters['to']);
            }
        }

        return $query->selectRaw('status, count(id) as total')
            ->groupBy('status')
            ->get();
    }

    public function reportCreatedByDate($filters = [])
    {
        $query = $this->model;

        $query = $this->addFilterCompanyQuery($query);

        if (!blank($filters)) {
            if (isset($filters['from'])) {
                $query = $query->where('created_at', '>=', $filters['from']);
            }
            if (isset($filters['to'])) {
                $query = $query->where('created_at', '<=', $filters['to']);
            }
        }

        return $query->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as created_date, count(id) as total")
            ->groupBy('created_date')
            ->get();
    }
}
