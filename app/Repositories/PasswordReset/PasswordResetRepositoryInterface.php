<?php
namespace App\Repositories\PasswordReset;

use App\Repositories\RepositoryInterface;

interface PasswordResetRepositoryInterface extends RepositoryInterface
{
    public function findByToken($token);

    public function findByEmail($email);

    public function updateByEmail($email, $token);

    public function deleteByEmail($email);
}
