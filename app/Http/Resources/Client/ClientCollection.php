<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use App\Http\Resources\BaseCollection;

class ClientCollection extends BaseCollection
{
    protected $totalClient;

    protected $totalClientCheckin;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  int  $totalClient
     * @return void
     */
    public function __construct($resource, $totalClient = 0, $totalClientCheckin = 0)
    {
        parent::__construct($resource);
        $this->totalClient = $totalClient;
        $this->totalClientCheckin = $totalClientCheckin;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'count'         => $this->collection->count(),
            'totalClient'   => $this->totalClient,
            'totalCheckin'  => $this->totalClientCheckin,
            'collection'    => parent::toArray($request),
            'pagination'    => $this->getPaginateMeta(),
        ];
    }
}
