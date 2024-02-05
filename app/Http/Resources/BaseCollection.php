<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
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
        return parent::toArray($request);
    }

    public function getPaginateMeta()
    {
        return [
            // 'links' => [
            //     'first'         => $this->url(1),
            //     'last'          => $this->url($this->lastPage()),
            //     'prev'          => $this->previousPageUrl(),
            //     'next'          => $this->nextPageUrl(),
            // ],
            'meta' => [
                'current_page'  => $this->currentPage(),
                'from'          => $this->firstItem(),
                'to'            => $this->lastItem(),
                'per_page'      => $this->perPage(),
                'total'         => $this->total(),
                'last_page'     => $this->lastPage(),
            ]
        ];
    }

    /* public function finalizeResult($request)
    {
        if (!empty($this->attrOnly)) {
            return $this->attrOnly;
        } else {
            $result = parent::toArray($request);

            if (!empty($this->attrMores)) {
                $result = array_merge(parent::toArray($request), $this->attrMores);
            }

            if (!empty($this->attrExcepts)) {
                foreach ($this->attrExcepts as $key) {
                    unset($result[$key]);
                }
            }

            $dateTimes = [
                'created_at' => Helper::getDateTimeFormat($this->created_at),
                'updated_at' => Helper::getDateTimeFormat($this->updated_at),
            ];

            return array_merge($result, $dateTimes);
        }
    } */
}
