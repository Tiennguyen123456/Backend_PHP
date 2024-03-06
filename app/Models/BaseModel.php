<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Site
 *
 * @property int $id
 * @property string $name
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|HelpCategory[] $help_categories
 * @property Collection|Help[] $helps
 * @property Collection|Page[] $pages
 * @property Collection|PostCategory[] $post_categories
 * @property Collection|Post[] $posts
 *
 * @package App\Models
 */
class BaseModel extends Model
{
    const STATUS_NEW            = 'NEW';
    const STATUS_ACTIVE         = 'ACTIVE';
    const STATUS_INACTIVE       = 'INACTIVE';
    const STATUS_DELETED        = 'DELETED';

    const STATUES = [
        self::STATUS_NEW        => 'New',
        self::STATUS_ACTIVE     => 'Active',
        self::STATUS_INACTIVE   => 'In-Active',
        self::STATUS_DELETED    => 'Deleted',
    ];

    const STATUES_VALID = [
        self::STATUS_NEW        => 'New',
        self::STATUS_ACTIVE     => 'Active',
        self::STATUS_INACTIVE   => 'In-Active',
    ];

    protected $tables = [
        'users' => [
            'log_name' => 'users',

        ],
    ];

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

    public function isNew()
    {
        return empty($this->id) ? true : false;
    }

    protected function getTableInfo($table)
    {
        return $this->tables[$table];
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults();
    // }

    public function scopeWithStatus($query)
    {
        $table = $this->getTable();

        if (Helper::tableHasColumn($table, 'status')) {
            return $query->where('status', '!=', self::STATUS_DELETED);
        }
    }
}
