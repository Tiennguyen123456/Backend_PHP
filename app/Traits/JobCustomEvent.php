<?php

namespace App\Traits;

use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Redis;

trait JobCustomEvent
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    protected function onComplete($eventId, $filePath)
	{
        logger('JobCustomEvent::onComplete');

        $limitRecord = 20;

        // Get duplicate index
        $arDuplicateIndex = [];

        $redisKey           = sprintf(config('redis.event.import.duplicate_index'), $eventId);
        $arDuplicateIndex   = Redis::lrange($redisKey, 0, $limitRecord);
        $len                = Redis::llen($redisKey);
        Redis::del($redisKey);

        // Notify row error
        if ($len > 0) {
            $message  = 'ImportClient for EventID: ' . $eventId . ' | ';
            $message .= 'Error: ' . $len . ' rows - ';
            $message .= implode(', ', $arDuplicateIndex);
            $message .= $len > $limitRecord ? '...' : '';
            $message .= ' has duplicate phone number';

            logger()->error($message);
        }

        // Remove file
        FileHelper::deleteFile($filePath);
	}
}
