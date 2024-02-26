<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Company
 *
 * @property int $id
 * @property int|null $parent_id
 * @property bool $is_default
 * @property string $name
 * @property int|null $limited_users
 * @property int|null $limited_events
 * @property int|null $limited_campaigns
 * @property string $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User|null $user
 * @property Company|null $company
 * @property Collection|Company[] $companies
 * @property Collection|Event[] $events
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Company extends BaseModel
{
	protected $casts = [
		'parent_id'         => 'int',
		'is_default'        => 'bool',
		'limited_users'     => 'int',
		'limited_events'    => 'int',
		'limited_campaigns' => 'int',
		'created_by'        => 'int',
		'updated_by'        => 'int'
	];

	protected $fillable = [
		'parent_id',
		'is_default',
		'code',
		'name',
        'tax_code',
        'contact_email',
        'contact_phone',
        'website',
        'address',
        'city',
		'limited_users',
		'limited_events',
		'limited_campaigns',
		'status',
		'created_by',
		'updated_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function company()
	{
		return $this->belongsTo(Company::class, 'parent_id');
	}

	public function companies()
	{
		return $this->hasMany(Company::class, 'parent_id');
	}

	public function events()
	{
		return $this->hasMany(Event::class);
	}

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
