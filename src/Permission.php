<?php

namespace Trunow\Rpac;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class Permission
 * @package Trunow\Rpac
 *
 * @property string $action
 * @property string $object
 * @property string $subject
 */
class Permission extends Model
{
    /**
     * @return Collection|Permission[]
     */
    public static function cached()
    {
        return Cache::rememberForever(__CLASS__.'\\Cache', function() {
            return Permission::all();
        });
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function(Permission $model) {
            Cache::forever(__CLASS__.'\\Cache', Permission::all());
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['object', 'subject', 'action'];
}
