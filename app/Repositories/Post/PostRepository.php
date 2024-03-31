<?php
namespace App\Repositories\Post;

use App\Repositories\Repository;

class PostRepository extends Repository implements PostRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Post::class;
    }
}
