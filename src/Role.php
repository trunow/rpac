<?php

namespace Trunow\Rpac;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(config('rpac.models.permission'));//->withTimestamps();
    }

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('rpac.models.user'))->withTimestamps();
    }

    /**
     * Only roles has permissions for entity's action.
     *
     * @param Builder $query
     * @param $action
     * @param $entity
     * @return Builder
     */
    public function scopeHasPermissionsForActionOfEntity(Builder $query, $action, $entity)
    {
        return $query->whereHas('permissions', function($query) use ($action, $entity) {
            $query->where('permissions.entity', '=', (is_string($entity) ? $entity : get_class($entity)));
            $query->where('permissions.action', '=', $action);
        });
    }

}
