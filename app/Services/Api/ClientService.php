<?php

namespace App\Services\Api;

use App\Imports\ClientImport;
use App\Models\Event;
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

    private function eventModel()
    {
        return Event::class;
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
            $this->attributes['pageSize'] ?? 50,
            $this->attributes['page'] ?? 1
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

    public function store()
    {
        $attrs = [
            'phone'         => $this->attributes['phone'],
            'email'         => $this->attributes['email'] ?? null,
            'group'         => $this->attributes['group'] ?? null,
            'fullname'      => $this->attributes['fullname'] ?? null,
            'address'       => $this->attributes['address'] ?? null,
            'type'          => $this->attributes['type'] ?? null,
            'status'        => $this->attributes['status'] ?? null,
            'is_checkin'    => $this->attributes['is_checkin'] ?? null,
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
            ];
        }

        return $this->storeAs($attrs, $attrMores);
    }

    public function checkin()
    {
        $model = $this->repo->find($this->attributes['id']);

        if (!empty($model)) {
            $model->update($this->attributes);
            return $model;
        }

        return false;
    }

    public function getAll()
    {
        return $this->repo->getAll(
            $this->getSearch(),
            $this->getFilters(),
            $this->attributes['orderBy'] ?? 'updated_at',
            $this->attributes['orderDesc'] ?? true,
            $this->attributes['limit'] ?? 0
        );
    }

    public function summary()
    {
        $eventId = $this->attributes['filters']['event_id'];

        $result = $this->repo->getSummary($this->getSearch(), $this->getFilters());

        // Get total client
        $total = 0;
        foreach ($result as $value) {
            $total += $value->total;
        }

        if (isset($this->attributes['filters']['is_checkin'])) {
            if (boolval($this->attributes['filters']['is_checkin'])) {
                return [
                    'totalClient' => $total,
                    'totalCheckin' => $total,
                    'groups' => $result
                ];
            } else {
                return [
                    'totalClient' => $total,
                    'totalCheckin' => 0,
                    'groups' => $result
                ];
            }
        }

        $this->attributes['filters'] = [
            'event_id' => $eventId,
            'is_checkin' => true,
        ];
        $totalClientCheckin = $this->count();

        return [
            'totalClient' => $total,
            'totalCheckin' => $totalClientCheckin,
            'groups' => $result
        ];
    }

    public function generateVariables(array $client)
    {
        $model = $this->eventModel();

        return [
            $model::MAIN_FIELD_CLIENT_FULLNAME => $client['fullname'],
            $model::MAIN_FIELD_CLIENT_EMAIL    => $client['email'],
            $model::MAIN_FIELD_CLIENT_PHONE    => $client['phone'],
            $model::MAIN_FIELD_CLIENT_ADDRESS  => $client['address'],
            $model::MAIN_FIELD_CLIENT_QRCODE   => $this->encodeQrData($client),
        ];
    }

    public function encodeQrData(array $client)
    {
        $arData = [
            md5($client['id'] . $client['event_id'] . $client['phone']),
            $client['id'],
            rand(10, 99),
            $client['event_id'],
            rand(10, 99),
            strrev($client['phone']),
            rand(10, 99),
        ];

        return implode(';', $arData);
    }

    public function decodeQrData(string $data)
    {
        $arData = explode(';', $data);

        if (count($arData) != 7) {
            return false;
        }

        $client = [
            'id' => (int) $arData[1],
            'event_id' => (int) $arData[3],
            'phone' => strrev($arData[5]),
        ];

        $postSecret = $arData[0];
        $secret = md5($client['id'] . $client['event_id'] . $client['phone']);

        if (strcmp($secret, $postSecret) != 0) {
            return false;
        }

        return $client;
    }
}
