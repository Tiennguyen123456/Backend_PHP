<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;
use App\Http\Resources\BaseCollection;

class RoleCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
