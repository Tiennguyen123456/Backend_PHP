<?php

namespace App\Http\Resources\User;

use App\Helpers\Helper;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class SelfResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $permissions = auth()->user()->getAllPermissions();

        $userPermissions = [];
        foreach ($permissions as $permission) {
            $userPermissions[] = $permission->name;
        }

        $this->attrMores = [
            'last_login_at' => Helper::getDateTimeFormat($this->last_login_at),
            'permissions'   => $userPermissions,
        ];

        $this->attrExcepts = [
            'email_verified_at'
        ];

        return $this->finalizeResult($request);
    }
}
