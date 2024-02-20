<?php

namespace App\Imports;

use App\Events\ClientImportedEvent;
use App\Models\Client;
use App\Services\Api\ClientService;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\ImportFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;

class ClientImport implements
    ToModel,
    SkipsEmptyRows,
    ShouldQueue,
    WithChunkReading,
    WithBatchInserts,
    WithHeadingRow,
    WithEvents
{
    use RemembersRowNumber;

    protected $arUnique = [];
    protected $eventId;
    protected $filePath;
    protected $clientService;

    public function __construct($eventId, $filePath)
    {
        $this->eventId = $eventId;
        $this->filePath = $filePath;
        $this->clientService = new ClientService();
    }

    private function setDuplicateRow()
    {
        $redisKey = sprintf(config('redis.event.import.duplicate_index'), $this->eventId);
        Redis::rpush($redisKey, $this->getRowNumber());
    }

    public function model(array $row)
    {
        $phone = $this->formatPhone($row['phone']);

        // Check duplicate in file
        if (in_array($phone, $this->arUnique)) {
            return $this->setDuplicateRow();
        }
        $this->arUnique[] = $phone;

        // Check duplicate in database
        if ($this->checkPhoneExistsInEvent($phone)) {
            return $this->setDuplicateRow();
        }

        return new Client([
            'event_id' => $this->eventId,
            'fullname' => $row['fullname'],
            'phone' => $phone,
            'email' => $row['email'],
            'address' => $row['address'],
        ]);
    }

    private function checkPhoneExistsInEvent($phone)
    {
        $filters = [
            'event_id'  => $this->eventId,
            'phone'     => $phone,
        ];
        $this->clientService->attributes['filters'] = $filters;

        return $this->clientService->count() > 0;
    }

    private function formatPhone($phone)
    {
        if (substr($phone, 0, 1) !== '0') {
            return '0' . $phone;
        }
        return $phone;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            ImportFailed::class => function (ImportFailed $event) {
                $data = [
                    'eventId' => $this->eventId,
                    'filePath' => $this->filePath,
                    'message' => $event->e->getMessage(),
                    'isSuccess' => false
                ];
                event(new ClientImportedEvent($data));
            },
            AfterImport::class => function () {
                $data = [
                    'eventId' => $this->eventId,
                    'filePath' => $this->filePath,
                    'isSuccess' => true
                ];
                event(new ClientImportedEvent($data));
            },
        ];
    }
}
