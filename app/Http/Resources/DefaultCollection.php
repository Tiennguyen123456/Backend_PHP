<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DefaultCollection extends BaseCollection
{
    protected $attrOnly;
    protected $attrMores;
    protected $attrExcepts;

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
