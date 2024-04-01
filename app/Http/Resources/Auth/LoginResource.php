<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class LoginResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->attrOnly = [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'type'          => $this->type,
            'access_token'  => $this->createToken($this->email)->plainTextToken,
            'token_type'    => 'Bearer'
        ];

        return $this->finalizeResult($request);
    }
}
