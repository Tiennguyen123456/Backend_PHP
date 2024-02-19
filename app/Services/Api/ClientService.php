<?php

namespace App\Services\Api;

use App\Helpers\FileHelper;
use App\Imports\ClientImport;
use App\Services\BaseService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use App\Repositories\Client\ClientRepository;

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

        if (!Storage::disk('public')->exists($filePath)) {
            return [
                'status' => 'error',
                'message' => 'FILE_NOT_FOUND'
            ];
        }

        $model = app($this->repo->getModel());
        $arImportColumn = $model->getImportColumns();

        $fullPath = Storage::disk('public')->path($filePath);

        # Validate header
        $headings = (new HeadingRowImport)->toArray($fullPath);
        $headings = $headings[0][0];

        foreach ($arImportColumn as $column) {
            if (!in_array($column, $headings)) {
                return [
                    'status' => 'error',
                    'message' => 'INVALID_HEADER'
                ];
            }
        }

        # Import
        Excel::queueImport(
            new ClientImport($eventId, $filePath),
            $fullPath
        );

        return [
            'status' => 'success'
        ];
    }
}
