<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends BaseModel
{
    use HasFactory;

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
}
