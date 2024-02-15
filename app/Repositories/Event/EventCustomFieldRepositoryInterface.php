<?php

namespace App\Repositories\Event;

use App\Repositories\RepositoryInterface;

interface EventCustomFieldRepositoryInterface extends RepositoryInterface
{
    public function getListByEventId($eventId);
}
