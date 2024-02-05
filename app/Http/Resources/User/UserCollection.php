<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseCollection;
use Illuminate\Http\Request;

class UserCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'count'         => $this->collection->count(),
            'collection'    => parent::toArray($request),
            'pagination'    => $this->getPaginateMeta(),
        ];
    }
}
