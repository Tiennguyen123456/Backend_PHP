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

        ];

        $this->attrExcepts = [

        ];

        return $this->finalizeResult($request);
    }
}
