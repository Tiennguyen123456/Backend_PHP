<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Client
 *
 * @property int $id
 * @property string $token
 * @property int|null $country_id
 * @property int $event_id
 * @property string $event_code
 * @property string $qrcode
 * @property string|null $email
 * @property string|null $phone
 * @property array|null $file_collection
 * @property array|null $custom_fields
 * @property string $type
 * @property string $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Country|null $country
 * @property User|null $user
 * @property Event $event
 *
 * @package App\Models
 */
class Client extends BaseModel
{
	protected $table = 'clients';

	protected $casts = [
		'event_id'      => 'int',
		'created_by'    => 'int',
		'updated_by'    => 'int',
        'is_checkin'    => 'int',
        'created_at'    => 'datetime:Y-m-d H:i:s',
		'created_at'    => 'datetime:Y-m-d H:i:s',
		'updated_at'    => 'datetime:Y-m-d H:i:s',
	];

	protected $fillable = [
		'event_id',
		'fullname',
		'email',
		'phone',
		'address',
        'is_checkin',
		'type',
		'status',
		'created_by',
		'updated_by'
	];

	protected $importColumns = [
		'fullname',
		'email',
		'phone',
		'address'
	];

	public function getImportColumns()
	{
		return $this->importColumns;
	}

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function event()
	{
		return $this->belongsTo(Event::class);
	}
}
