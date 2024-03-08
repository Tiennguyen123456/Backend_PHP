<?php

namespace App\Repositories\Client;

use App\Repositories\RepositoryInterface;

interface ClientRepositoryInterface extends RepositoryInterface
{
    // public function getList($searches = [], $filters = [], $orderByColumn = 'updated_at', $orderByDesc = true, $limit = 0, $paginate = 50);

    public function getClientsByEventId(
        $eventId,
        $searches = [],
        $filters = [],
        $orderByColumn = 'updated_at',
        $orderByDesc = true,
        $limit = 0,
        $paginate = 50,
        $page
    );

    public function getClientByEventIdQrcode($eventId, $qrcode, $status = null);

    public function getClientByQrcode($qrcode, $status = null, $buildQuery = false, $query = null);
}
