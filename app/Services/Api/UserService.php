<?php
namespace App\Services\Api;

use Illuminate\Support\Str;
use App\Services\BaseService;
use App\Events\UserCreatedEvent;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepository;

class UserService extends BaseService
{
    public function __construct()
    {
        $this->repo = new UserRepository();
    }

    public function role()
    {
        return new RoleService();
    }

    public function getList()
    {
        // $filterMores = [
        //     'role_id',
        //     'status'
        // ];

        return $this->repo->getList(
            $this->getSearch(),
            $this->getFilters(),
            $this->attributes['orderBy'] ?? 'updated_at',
            $this->attributes['orderDesc'] ?? true,
            $this->attributes['limit'] ?? null,
            $this->attributes['pageSize'] ?? 50
        );
    }

    public function getDetail($id)
    {
        $user = $this->find($id);

        if ($user) {
            return $user;
        }

        return null;
    }

    public function store()
    {
        $attrs = [
            'name'              => $this->attributes['name'],
            'username'          => $this->attributes['username'],
            'email'             => $this->attributes['email'],
            'password'          => Hash::make(Str::random(20)),
            'status'            => $this->attributes['status'] ?? null,
        ];

        $roleId = $this->attributes['role_id'];
        $role = $this->role()->find($roleId);

        if (!isset($this->attributes['id'])) {
            $attrMores = [
                'created_by'    => auth()->user()->id,
                'updated_by'    => auth()->user()->id
            ];

            $user = $this->repo->create(array_merge($attrs, $attrMores));
            $user->syncRoles([$role->name]);

            event(new UserCreatedEvent($user));
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
            ];

            $user = $this->repo->update($this->attributes['id'], array_merge($attrs, $attrMores));
            if ($user)
                $user->syncRoles([$role->name]);
        }

        return $user;
    }

    public function findByEmail($email)
    {
        return $this->repo->findByEmail($email);
    }
}
