<?php

namespace App\Services\Api;

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
            'title'             => $this->attributes['title'] ?? null,
            'subtitle'          => $this->attributes['subtitle'] ?? null,
            'content'           => $this->attributes['content'] ?? null,
            'form_enable'       => $this->attributes['form_enable'] ?? null,
            'form_title'        => $this->attributes['form_title'] ?? null,
            'form_content'      => $this->attributes['form_content'] ?? null,
            'form_input'        => $this->attributes['form_input'] ?? null,
        ];

        if (!isset($this->attributes['id'])) {
            $attrMores = [
                'event_id'      => $this->attributes['event_id'],
                'company_id'    => $this->attributes['company_id'],
                'created_by'    => auth()->user()->id,
                'updated_by'    => auth()->user()->id,
            ];

            return $this->repo->create(array_merge($attrs, $attrMores));
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
            ];

            return $this->repo->update($this->attributes['id'], array_merge($attrs, $attrMores));
        }
    }
}
