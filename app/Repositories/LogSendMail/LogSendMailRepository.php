<?php
namespace App\Repositories\LogSendMail;

use App\Repositories\Repository;

class LogSendMailRepository extends Repository implements LogSendMailRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\LogSendMail::class;
    }
}
