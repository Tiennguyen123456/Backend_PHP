<?php
namespace App\Repositories\Client;

use App\Repositories\Repository;

class ClientRepository extends Repository implements ClientRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Client::class;
    }

    public function getList($searches = [], $filters = [], $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0, $paginate = 50)
    {
        $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED);
        $query = $this->addSearchQuery($query, $searches);

        /* FILTER */

        if (count($filters)) {
            if (isset($filters['status'])) {
                if (is_array($filters['status'])) {
                    $query = $query->whereIn('status', $filters['status']);
                } else {
                    $query = $query->where([
                        'status' => $filters['status']
                    ]);
                }
            }

            if (isset($filters['type'])) {
                if (is_array($filters['type'])) {
                    $query = $query->whereIn('type', $filters['type']);
                } else {
                    $query = $query->where([
                        'type' => $filters['type']
                    ]);
                }
            }

            if (isset($filters['country_id'])) {
                $query = $query->where('country_id', $filters['country_id']);
            }

            if (isset($filters['from_date'])) {
                $query = $query->whereDate('created_at', '>=', $filters['from_date']);
            }

            if (isset($filters['to_date'])) {
                $query = $query->whereDate('created_at', '<=', $filters['to_date']);
            }
        }

        if ($orderByDesc) {
            $query = $query->orderBy($orderByColumn, 'desc');
        } else {
            $query = $query->orderBy($orderByColumn, 'asc');
        }

        if ($limit > 0) {
            $query = $query->limit($limit);
        }

        if ($paginate > 0) {
            return $query->paginate($paginate);
        }

        return $query->get();
    }

    public function getClientsByEventId($eventId, $searches = [], $filters = [], $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0, $paginate = 50)
    {
        $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED)
                            ->where('event_id', '=', $eventId);

        $query = $this->addSearchQuery($query, $searches);

        if (count($filters)) {
            if (isset($filters['status'])) {
                if (is_array($filters['status'])) {
                    $query = $query->whereIn('status', $filters['status']);
                } else {
                    $query = $query->where([
                        'status' => $filters['status']
                    ]);
                }
            }

            if (isset($filters['type'])) {
                if (is_array($filters['type'])) {
                    $query = $query->whereIn('type', $filters['type']);
                } else {
                    $query = $query->where([
                        'type' => $filters['type']
                    ]);
                }
            }

            if (isset($filters['country_id'])) {
                $query = $query->where('country_id', $filters['country_id']);
            }

            if (isset($filters['from_date'])) {
                $query = $query->whereDate('created_at', '>=', $filters['from_date']);
            }

            if (isset($filters['to_date'])) {
                $query = $query->whereDate('created_at', '<=', $filters['to_date']);
            }
        }

        if ($orderByDesc) {
            $query = $query->orderBy($orderByColumn, 'desc');
        } else {
            $query = $query->orderBy($orderByColumn, 'asc');
        }

        if ($limit > 0) {
            $query = $query->limit($limit);
        }

        if ($paginate > 0) {
            return $query->paginate($paginate);
        }

        return $query->get();
    }

    public function getClientByEventIdQrcode($eventId, $qrcode, $status = null)
    {
        $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED)
                            ->where('event_id', $eventId);

        $query = $this->getClientByQrcode($qrcode, $status, true, $query);
        return $query->first();
    }

    public function getClientByQrcode($qrcode, $status = null, $buildQuery = false, $query = null)
    {
        if ($buildQuery) {
            $query = $query->where('qrcode', $qrcode);
        } else {
            $query = $this->model->where('status', '!=', $this->model::STATUS_DELETED)
                                ->where('qrcode', '=', $qrcode);
        }

        if (!empty($status)) {
            if (is_array($status)) {
                $query = $query->whereIn('status', $status);
            } else {
                $query = $query->where(['status' => $status]);
            }
        }

        if ($buildQuery) {
            return $query;
        }

        return $query->first();
    }
}
