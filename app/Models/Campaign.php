<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends BaseModel
{
    use HasFactory;

    // Status
    const STATUS_NEW        = 'NEW';
    const STATUS_RUNNING    = 'RUNNING';
    const STATUS_PAUSED     = 'PAUSED';
    const STATUS_STOPPED    = 'STOPPED';
    const STATUS_FINISHED   = 'FINISHED';
    const STATUS_DELETED    = 'DELETED';

    const STATUES_VALID = [
        self::STATUS_NEW        => 'New',
        self::STATUS_RUNNING    => 'Running',
        self::STATUS_PAUSED     => 'Paused',
        self::STATUS_STOPPED    => 'Stopped',
        self::STATUS_FINISHED   => 'Finished',
        self::STATUS_DELETED    => 'Deleted',
    ];

    // Action
    const ACTION_STOP  = 'STOP';
    const ACTION_START = 'START';
    const ACTION_PAUSE = 'PAUSE';

    const ACTIONS_VALID = [
        self::ACTION_STOP    => 'Stop',
        self::ACTION_START   => 'Start',
        self::ACTION_PAUSE   => 'Pause',
    ];

    protected $casts = [
        'event_id'      => 'int',
        'company_id '   => 'int',
        'run_time'      => 'datetime:Y-m-d H:i:s',
		'created_by'    => 'int',
		'updated_by'    => 'int',
        'created_at'    => 'datetime:Y-m-d H:i:s',
		'updated_at'    => 'datetime:Y-m-d H:i:s',
	];

    protected $fillable = [
        'name',
        'company_id',
        'event_id',
        'run_time',
        'filter_client',
        'status',
        'mail_content',
        'mail_subject',
        'sender_email',
        'sender_name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function event()
	{
		return $this->belongsTo(Event::class);
	}

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    static public function getActions()
    {
        return self::ACTIONS_VALID;
    }
}
