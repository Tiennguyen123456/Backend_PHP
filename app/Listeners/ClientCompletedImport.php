<?php

namespace App\Listeners;

use App\Helpers\FileHelper;
use App\Events\ClientImportedEvent;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientCompletedImport
{
    protected $limitRecord = 20;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ClientImportedEvent $event): void
    {
        logger('ClientCompletedImport');

        $arData = $event->data;
        $isSuccess = $arData['isSuccess'] ?? false;
        $eventId = $arData['eventId'];
        $filePath = $arData['filePath'];
        $message = $arData['message'] ?? null;

        // Remove file
        FileHelper::deleteFile($filePath);

        // Get duplicate index
        $redisKey           = sprintf(config('redis.event.import.duplicate_index'), $eventId);
        $arDuplicateIndex   = Redis::lrange($redisKey, 0, $this->limitRecord);
        $len                = Redis::llen($redisKey);
        Redis::del($redisKey);

        // Notify row error
        if ($len > 0) {
            $message  = 'ImportClient for EventID: ' . $eventId . ' | ';
            $message .= 'Error: ' . $len . ' rows - ';
            $message .= implode(', ', array_unique($arDuplicateIndex));
            $message .= $len > $this->limitRecord ? '...' : '';
            $message .= ' has duplicate phone number';

            logger()->error($message);
        }

        if ($isSuccess) {
            logger('SUCCESS EventID: ' . $eventId);
        } else {
            logger()->error('FAILED EventID: ' . $eventId . ' with message: ' . $message);
        }
    }
}
