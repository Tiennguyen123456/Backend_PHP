<?php

namespace App\Http\Resources\Campaign;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class CampaignResource extends BaseResource
{
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
            'company'       => $this->company()->withStatus()->first(['id', 'name']),
            'event'         => $this->event()->withStatus()->first(['id', 'name']),
            'filter_client' => unserialize($this->filter_client),
        ];

        $this->attrExcepts = [

        ];

        return $this->finalizeResult($request);
    }
}
