<?php

namespace App\Services\Api;

use App\Repositories\PasswordReset\PasswordResetRepository;
use App\Services\BaseService;

class PasswordResetService extends BaseService
{
    public function __construct()
    {
        $this->repo = new PasswordResetRepository();
    }

    public function store()
    {
        $email = isset($this->attributes['email']) ? $this->attributes['email'] : null;
        $token = isset($this->attributes['token']) ? $this->attributes['token'] : null;

        $model = $this->repo->findByEmail($email);
        if (empty($model)) {
            $model = $this->repo->create($this->attributes);
        } else {
            $model = $this->repo->updateByEmail($email, $token);
        }

        return $model ?? false;
    }

    public function findByToken($token)
    {
        return $this->repo->findByToken($token);
    }

    public function deleteByEmail($token)
    {
        return $this->repo->deleteByEmail($token);
    }
}
