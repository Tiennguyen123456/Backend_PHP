<?php

namespace App\Services\Api;

use App\Helpers\FileHelper;
use App\Services\BaseService;
use App\Repositories\Post\PostRepository;

class PostService extends BaseService
{
    public function __construct()
    {
        $this->repo = new PostRepository();
    }

    public function store()
    {
        $attrs = [
            'name'              => $this->attributes['name'],
            'slug'              => $this->attributes['slug'],
            'form_enable'       => $this->attributes['form_enable'] ?? false,
            'title'             => $this->attributes['title'] ?? null,
            'subtitle'          => $this->attributes['subtitle'] ?? null,
            'content'           => $this->attributes['content'] ?? null,
            'form_title'        => $this->attributes['form_title'] ?? null,
            'form_content'      => $this->attributes['form_content'] ?? null,
            'form_input'        => $this->attributes['form_input'] ?? null,
            'status'            => $this->attributes['status'] ?? null,
        ];

        if (isset($this->attributes['background_img'])) {
            $attrs['background_img'] = $this->attributes['background_img'];
        }

        if (!isset($this->attributes['id'])) {
            $attrMores = [
                'event_id'      => $this->attributes['event_id'],
                'company_id'    => $this->attributes['company_id'],
                'created_by'    => auth()->user()->id,
                'updated_by'    => auth()->user()->id,
                'unique_id'     => uniqid(),
            ];

            return $this->repo->create(array_merge($attrs, $attrMores));
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
                'updated_at'    => now(),
            ];

            return $this->repo->update($this->attributes['id'], array_merge($attrs, $attrMores));
        }
    }

    public function deleteBackgroundImg($model)
    {
        if (!FileHelper::fileExists($model->background_img)) {
            return false;
        }

        FileHelper::deleteFile($model->background_img);

        $this->attributes['id'] = $model->id;

        $attrs = [
            'background_img' => null,
            'updated_by'     => auth()->user()->id,
            'updated_at'     => now(),
        ];

        return $this->repo->update($this->attributes['id'], $attrs);
    }

    public function findByUniqueId($uniqueId)
    {
        return $this->repo->findByUniqueId($uniqueId);
    }

    public function findBySlug($slug)
    {
        return $this->repo->findBySlug($slug);
    }
}
