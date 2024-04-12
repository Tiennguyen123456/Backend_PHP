<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Company;
use App\Models\BaseModel;

class Post extends BaseModel
{
    protected $fillable = [
        'unique_id',
        'company_id',
        'event_id',
        'name',
        'slug',
        'title',
        'subtitle',
        'content',
        'background_img',
        'form_enable',
        'form_title',
        'form_content',
        'form_input',
        'status',
        'created_by',
        'updated_by',
        'updated_at',
    ];

    protected $casts = [
        'event_id'      => 'integer',
        'company_id'    => 'integer',
        'form_enable'   => 'boolean',
        'form_input'    => 'array',
    ];

    public function event()
	{
		return $this->belongsTo(Event::class);
	}

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
