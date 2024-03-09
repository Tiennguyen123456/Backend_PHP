<?php

namespace App\Http\Resources\LogSendEmail;

use Illuminate\Http\Request;
use App\Http\Resources\BaseCollection;

class LogSendEmailCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'collection'    => $this->finalizeCollection(),
            'pagination'    => $this->getPaginateMeta(),
        ];
    }

    public function finalizeCollection()
    {
        return $this->collection->map(function ($logSendEmail) {
            $data = $logSendEmail->toArray();

            $data['client'] = $logSendEmail->client()->first(['id', 'fullname']);

            return $data;
        });
    }
}
