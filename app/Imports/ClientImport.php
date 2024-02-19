<?php

namespace App\Imports;

use App\Models\Client;
use App\Traits\JobCustomEvent;
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
    use JobCustomEvent, RemembersRowNumber;

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

    private function setDuplicateIndex()
    {
        $redisKey = sprintf(config('redis.event.import.duplicate_index'), $this->eventId);
        Redis::rpush($redisKey, $this->getRowNumber());
    }

    public function model(array $row)
    {
        $phone = $row['phone'];

        // Check duplicate in file
        if (in_array($phone, $this->arUnique)) {
            return $this->setDuplicateIndex();
        }
        $this->arUnique[] = $phone;

        // Check duplicate in database
        $filters = [
            'event_id'  => $this->eventId,
            'phone'     => $phone,
        ];
        $this->clientService->attributes['filters'] = $filters;

        if ($this->clientService->count() > 0) {
            return $this->setDuplicateIndex();
        }

        return new Client([
            'event_id' => $this->eventId,
            'fullname' => $row['fullname'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'address' => $row['address'],
        ]);
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
                $this->onFailed($this->eventId, $this->filePath, $event->e->getMessage());
                $this->onComplete($this->eventId, $this->filePath);
            },
            AfterImport::class => function () {
                $this->onComplete($this->eventId, $this->filePath);
            },
        ];
    }
}