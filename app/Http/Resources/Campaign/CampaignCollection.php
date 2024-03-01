<?php

namespace App\Http\Resources\Campaign;

use Illuminate\Http\Request;
use App\Http\Resources\BaseCollection;

class CampaignCollection extends BaseCollection
{

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'collection'    => parent::toArray($request),
            'pagination'    => $this->getPaginateMeta(),
        ];
    }
}
