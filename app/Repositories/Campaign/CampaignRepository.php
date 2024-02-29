<?php
namespace App\Repositories\Campaign;

use App\Repositories\Repository;

class CampaignRepository extends Repository implements CampaignRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Campaign::class;
    }
}
