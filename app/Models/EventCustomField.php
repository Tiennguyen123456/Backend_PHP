<?php

namespace App\Models;

use App\Models\BaseModel;

class EventCustomField extends BaseModel
{
    protected $fillable = [
        'event_id',
		'code',
		'name',
        'value',
		'status',
		'created_by',
		'updated_by'
	];

    protected $casts = [
        'event_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
		'created_at' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

	protected $hidden = [
		'status'
	];

	public function events()
	{
		return $this->belongsTo(Event::class);
	}
}
