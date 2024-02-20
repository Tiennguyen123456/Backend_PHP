<?php

namespace App\Listeners;

use App\Events\ClientImportedEvent;
use App\Services\Api\ClientService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventUpdateClient
{
    protected $service;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->service = new ClientService();
    }

    /**
     * Handle the event.
     */
    public function handle(ClientImportedEvent $event): void
    {
        $arData = $event->data;
        $eventId = $arData['eventId'];

        $filters = [
            'event_id'  => $eventId,
        ];
        $this->service->attributes['filters'] = $filters;
        $totalClient = $this->service->count();
        $keyTotalClient = sprintf(config('redis.event.client.total'), $eventId);
        Redis::set($keyTotalClient, $totalClient);

        $filters = [
            'event_id'  => $eventId,
            'is_checkin' => 1
        ];
        $this->service->attributes['filters'] = $filters;
        $totalClientCheckin = $this->service->count();
        $keyTotalClientCheckin = sprintf(config('redis.event.client.checkin'), $eventId);
        Redis::set($keyTotalClientCheckin, $totalClientCheckin);
    }
}
