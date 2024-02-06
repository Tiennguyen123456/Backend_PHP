<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->attrMores = [
            'last_login_at' => Helper::getDateTimeFormat($this->last_login_at),
            'roles'         => $this->getRoleNames(),
        ];

        $this->attrExcepts = [
            'email_verified_at'
        ];

        return $this->finalizeResult($request);
    }
}
