<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Storage;

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
            'company'           => $this->company()->withStatus()->first(['id', 'name']),
            'event'             => $this->event()->withStatus()->first(['id', 'name']),
            'background_img'    => $this->background_img ? asset(Storage::url($this->background_img)) : null,
        ];

        $this->attrExcepts = [

        ];

        return $this->finalizeResult($request);
    }
}
