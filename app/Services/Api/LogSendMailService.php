<?php
namespace App\Services\Api;

use App\Repositories\LogSendMail\LogSendMailRepository;
use App\Services\BaseService;

class LogSendMailService extends BaseService
{
    public function __construct()
    {
        $this->repo = new LogSendMailRepository();
    }
}
