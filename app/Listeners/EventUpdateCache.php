<?php

namespace App\Listeners;

use App\Events\ClientImportedEvent;
use App\Services\Api\ClientService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventUpdateCache
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

        $this->service->updateCache($eventId);
    }
}
