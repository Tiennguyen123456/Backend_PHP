<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSendMail extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'campaign_id'   => 'int',
        'client_id'     => 'int',
        'created_at'    => 'datetime:Y-m-d H:i:s',
		'updated_at'    => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'campaign_id',
        'client_id',
        'email',
        'subject',
        'content',
        'status',
        'error',
        'sent_at',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
