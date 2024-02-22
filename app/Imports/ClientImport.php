<?php

namespace App\Imports;

use App\Models\Client;
use App\Helpers\FileHelper;
use App\Services\Api\ClientService;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
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
    protected $limitRecord = 20;
    protected $redisDuplicateKey;

    public function __construct($eventId, $filePath)
    {
        $this->eventId = $eventId;
        $this->filePath = $filePath;
        $this->clientService = new ClientService();
        $this->redisDuplicateKey = sprintf(config('redis.event.import.duplicate_index'), $eventId);
    }

    private function setDuplicateRow()
    {
        Redis::rpush($this->redisDuplicateKey, $this->getRowNumber());
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
            'group' => $row['group'] ?? '',
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
            BeforeImport::class => function () {
                Redis::del($this->redisDuplicateKey);
            },
            ImportFailed::class => function (ImportFailed $event) {
                $this->eventImportFailed($event->e->getMessage());
                $this->eventComplete();
            },
            AfterImport::class => function () {
                $this->eventComplete();
            },
        ];
    }

    private function eventComplete()
    {
        $eventId = $this->eventId;

        logger('ImportComplete for EventID: ' . $eventId);

        // Remove file
        FileHelper::deleteFile($this->filePath);

        // Get duplicate index
        $redisKey           = $this->redisDuplicateKey;
        $arDuplicateIndex   = Redis::lrange($redisKey, 0, $this->limitRecord);
        $len                = Redis::llen($redisKey);
        Redis::del($redisKey);

        // Notify row error
        if ($len > 0) {
            $message  = 'ErrorDuplicate - ' . $len . ' rows error: ';
            $message .= implode(', ', array_unique($arDuplicateIndex));
            $message .= $len > $this->limitRecord ? '...' : '';

            logger()->error($message);
        }

        // Update cache
        $this->clientService->updateCache($eventId);
    }

    private function eventImportFailed($message)
    {
        logger()->error('ImportFailed EventID: ' . $this->eventId . ' with message: ' . $message);
    }
}
