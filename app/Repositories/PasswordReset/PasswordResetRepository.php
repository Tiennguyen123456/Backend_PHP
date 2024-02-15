<?php
namespace App\Repositories\PasswordReset;

use App\Repositories\Repository;

class PasswordResetRepository extends Repository implements PasswordResetRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\PasswordReset::class;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByToken($token)
    {
        return $this->model->where('token', $token)->first();
    }

    public function updateByEmail($email, $token)
    {
        $query = $this->model->where('email', $email);
        return $query->update(['token' => $token]);
    }

    public function deleteByEmail($email)
    {
        return $this->model->where('email', $email)->delete();
    }
}
