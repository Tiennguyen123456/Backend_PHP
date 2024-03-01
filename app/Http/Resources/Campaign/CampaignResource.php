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
            'company'   => $this->company()->first(['id', 'name']),
            'event'     => $this->event()->first(['id', 'name']),
        ];

        $this->attrExcepts = [

        ];

        return $this->finalizeResult($request);
    }
}
