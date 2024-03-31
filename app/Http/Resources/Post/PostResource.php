<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class PostResource extends BaseResource
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
        ];

        $this->attrExcepts = [

        ];

        return $this->finalizeResult($request);
    }
}
