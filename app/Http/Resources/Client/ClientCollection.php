<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use App\Services\Api\ClientService;
use App\Http\Resources\BaseCollection;

class ClientCollection extends BaseCollection
{
    private function clientService()
    {
        return new ClientService();
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $eventId = $request->id;

        $totalClient = $this->clientService()->getCountClientByEventId($eventId) ?? 0;
        $totalCheckin = $this->clientService()->getCountClientCheckinByEventId($eventId) ?? 0;

        return [
            'count'         => $this->collection->count(),
            'totalClient'   => $totalClient,
            'totalCheckin'  => $totalCheckin,
            'collection'    => parent::toArray($request),
            'pagination'    => $this->getPaginateMeta(),
        ];
    }
}
