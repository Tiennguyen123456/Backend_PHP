<?php
namespace App\Repositories\Post;

use App\Helpers\Helper;
use App\Repositories\Repository;

class PostRepository extends Repository implements PostRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Post::class;
    }

    public function findByUniqueId($uniqueId)
    {
        $query = $this->model;

        if (Helper::tableHasColumn($this->getModelTable(), 'status')) {
            $query = $query->where('status', '!=', $query::STATUS_DELETED);
        }

        return $this->model->where('unique_id', $uniqueId)->first();
    }
}
