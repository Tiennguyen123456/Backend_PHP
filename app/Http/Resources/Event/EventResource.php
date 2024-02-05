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
            // 'client_count'  => $this->client()->getClientsByEventId($this->id)->count(),
            'start_time'     => Helper::getDateFormat($this->start_time),
            'end_time'      => Helper::getDateFormat($this->end_time),
        ];

        $this->attrExcepts = [
            'main_field_templates',
            'custom_field_templates',
            'languages'
        ];

        return $this->finalizeResult($request);
    }
}
