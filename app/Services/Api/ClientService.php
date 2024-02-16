<?php

namespace App\Services\Api;

use App\Imports\ClientImport;
use App\Repositories\Client\ClientRepository;
use App\Services\BaseService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ClientService extends BaseService
{
    public function __construct()
    {
        $this->repo = new ClientRepository();
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

    public function import()
    {
        $eventId = $this->attributes['event_id'];
        $filePath = $this->attributes['filePath'];

        if (Storage::disk('public')->exists($filePath)) {
            Excel::queueImport(
                new ClientImport($eventId, $filePath),
                Storage::disk('public')->path($filePath)
            );
            return true;
        } else {
            return false;
        }
    }
}
