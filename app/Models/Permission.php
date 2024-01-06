<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Role[] $roles
 * @property Collection|UserPermission[] $user_permissions
 *
 * @package App\Models
 */
class Permission extends BaseModel
{
	protected $table = 'permissions';

	protected $fillable = [
		'name',
		'guard_name'
	];

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'role_permissions');
	}

	public function user_permissions()
	{
		return $this->hasMany(UserPermission::class);
	}
}
