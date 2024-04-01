<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
// use Spatie\Permission\Traits\HasPermissions;
// use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * Class User
 *
 * @property int $id
 * @property bool $is_admin
 * @property Carbon|null $expire_date
 * @property string $name
 * @property string $username
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string $type
 * @property string|null $gate
 * @property string|null $avatar_path
 * @property string|null $note
 * @property string $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User|null $user
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $table = 'users';
    protected $guard_name = 'api';

    const TYPE_SYSTEM_ADMIN         = 'SYSTEM_ADMIN';
    const TYPE_ADMIN                = 'ADMIN';
    const TYPE_USER_APP             = 'USER_APP';
    const TYPE_DEVICE_MOBILE        = 'DEVICE_MOBILE';
    const TYPE_DEVICE_PC            = 'DEVICE_PC';

    const TYPES = [
        self::TYPE_SYSTEM_ADMIN     => 'System Admin',
        self::TYPE_ADMIN            => 'Admin',
        self::TYPE_USER_APP         => 'App',
        self::TYPE_DEVICE_MOBILE    => 'Mobile',
        self::TYPE_DEVICE_PC        => 'PC',
    ];

    const STATUS_NEW                = 'NEW';
    const STATUS_ACTIVE             = 'ACTIVE';
    const STATUS_INACTIVE           = 'INACTIVE';
    const STATUS_DELETED            = 'DELETED';

    const STATUES = [
        self::STATUS_NEW            => 'New',
        self::STATUS_ACTIVE         => 'Active',
        self::STATUS_INACTIVE       => 'In-Active',
        self::STATUS_DELETED        => 'Deleted',
    ];

    const STATUES_VALID = [
        self::STATUS_NEW            => 'New',
        self::STATUS_ACTIVE         => 'Active',
        self::STATUS_INACTIVE       => 'In-Active',
    ];

    const STATUES_GRANTED = [
        self::STATUS_NEW            => 'New',
        self::STATUS_ACTIVE         => 'Active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'company_id'        => 'int',
		'event_id'          => 'int',
		'is_admin'          => 'bool',
		'expire_date'       => 'datetime',
		'email_verified_at' => 'datetime',
		'created_by'        => 'int',
		'updated_by'        => 'int',
		'last_login_at'     => 'datetime'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
		'event_id',
		'is_admin',
		'expire_date',
		'name',
		'email',
		'email_verified_at',
		'password',
		'type',
		'gate',
		'avatar_path',
		'note',
		'status',
		'created_by',
		'updated_by',
		'remember_token',
		'last_login_at'
    ];

    protected $logAttributes = [
        'company_id',
		'event_id',
        'expire_date',
        'name',
		'email',
        'type',
		'gate',
		'avatar_path',
        'note',
		'status',
		'created_by',
		'updated_by',
    ];

    /* RELATIONSHIP */

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	public function event()
	{
		return $this->belongsTo(Event::class);
	}

	public function checkins()
	{
		return $this->hasMany(Checkin::class);
	}

	public function clients()
	{
		return $this->hasMany(Client::class, 'updated_by');
	}

	public function companies()
	{
		return $this->hasMany(Company::class, 'updated_by');
	}

	public function countries()
	{
		return $this->hasMany(Country::class, 'updated_by');
	}

	public function event_assets()
	{
		return $this->hasMany(EventAsset::class, 'updated_by');
	}

	public function event_settings()
	{
		return $this->hasMany(EventSetting::class, 'updated_by');
	}

	public function events()
	{
		return $this->hasMany(Event::class, 'updated_by');
	}

	public function export_logs()
	{
		return $this->hasMany(ExportLog::class, 'updated_by');
	}

	public function language_defines()
	{
		return $this->hasMany(LanguageDefine::class, 'updated_by');
	}

	public function languages()
	{
		return $this->hasMany(Language::class, 'updated_by');
	}

	public function organizers()
	{
		return $this->hasMany(Organizer::class, 'updated_by');
	}

	public function qrcode_templates()
	{
		return $this->hasMany(QrcodeTemplate::class, 'updated_by');
	}

	public function users()
	{
		return $this->hasMany(User::class, 'updated_by');
	}

    /* CONST FUNCTIONS */

    static public function getStatues()
    {
        return self::STATUES;
    }

    public function getStatusText()
    {
        return self::STATUES[$this->status];
    }

    static public function getStatuesValid()
    {
        return self::STATUES_VALID;
    }

    static public function getStatuesGranted()
    {
        return self::STATUES_GRANTED;
    }

    static public function getTypes()
    {
        return self::TYPES;
    }

    public function getTypeText()
    {
        return self::TYPES[$this->status];
    }

    public function isNew()
    {
        return empty($this->id) ? true : false;
    }

    /* ACTIVITY LOG */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->logAttributes ?? [])
            ->logOnlyDirty()
            ->useLogName(parent::getTable());
            // ->useLogName(parent::getTableInfo($this->table)['log_name']);
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->description = $eventName;
        // $activity->description = "activity.logs.message.{$eventName}";
    }
}
