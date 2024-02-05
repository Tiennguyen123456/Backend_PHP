<?php
namespace App\Services\Api;

use App\Models\Client;
use App\Repositories\Client\ClientRepository;
use App\Services\BaseService;

class ClientService extends BaseService
{
    public function __construct()
    {
        $this->repo = new ClientRepository();
    }

    public function getList()
    {
        $filterMores = [
            'start_time',
            'to_date'
        ];

        return $this->repo->getList(
            $this->getSearch(),
            $this->getFilters($filterMores),
            $this->attributes['orderBy'] ?? 'updated_at',
            $this->attributes['orderDesc'] ?? true,
            $this->attributes['limit'] ?? null,
            $this->attributes['pageSize'] ?? 50
        );
    }

    public function getClientsByEventId($eventId)
    {
        $filterMores = [
            'start_time',
            'to_date'
        ];

        return $this->repo->getClientsByEventId(
            $eventId,
            $this->getSearch(),
            $this->getFilters($filterMores),
            $this->attributes['orderBy'] ?? 'updated_at',
            $this->attributes['orderDesc'] ?? true,
            $this->attributes['limit'] ?? null,
            $this->attributes['pageSize'] ?? 50
        );
    }
}
