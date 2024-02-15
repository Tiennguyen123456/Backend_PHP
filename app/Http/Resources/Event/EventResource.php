<?php

namespace App\Http\Resources\Event;

use App\Helpers\Helper;
use App\Http\Resources\BaseResource;
use App\Services\Api\ClientService;
use Illuminate\Http\Request;

class EventResource extends BaseResource
{
    public function client()
    {
        return new ClientService();
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->attrOnly = [

        ];

        $this->attrMores = [

        ];

        $this->attrExcepts = [

        ];

        return $this->finalizeResult($request);
    }
}
