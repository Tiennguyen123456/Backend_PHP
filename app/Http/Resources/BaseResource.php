<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected $attrOnly;
    protected $attrMores;
    protected $attrExcepts;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

        // DateTime::make('updated_at')->sortable()->readOnly()->serializeUsing(
        //     static fn($value) => Carbon::parse($value)->format(config('app.date_format'))
        // )

        // return array_merge(
        //     parent::toArray($request),
        //     [
        //         'clients_count' => $this->whenLoaded('clients', function () {
        //             return $this->clients->count();
        //         }),
        //         'clients' => ClientResource::collection($this->whenLoaded('clients')),
        //     ]
        // );
    }

    protected function finalizeResult($request)
    {
        if (!empty($this->attrOnly)) {
            return $this->attrOnly;
        } else {
            $result = parent::toArray($request);

            if (!empty($this->attrMores)) {
                $result = array_merge($result, $this->attrMores);
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
    }
}
