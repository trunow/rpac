<?php

/**
 *  Трэйт контроля доступа для модели User на основе ролей
 */

namespace Trunow\Rpac\Traits;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;

trait Rpacable
{
    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('rpac.models.role'))->withTimestamps();
    }

    public function rolesForActionOfEntity($action, $entity)
    {
        return $this->roles()->hasPermissionsForActionOfEntity($action, $entity);
    }

    /**
     * Check if the user has a role or roles.
     *
     * @param int|string|array $role
     * @param bool $all
     * @return bool
     */
    public function is($role, $useAnd = false)
    {
        return $this->{$this->buildMethodName('is', $useAnd)}($role);
    }

    /**
     * Check if the user has at least one role.
     *
     * @param int|string|array $role
     * @return bool
     */
    public function isOr($roles)
    {
        return !!$this->roles->whereIn('slug', (array)$roles)->count();
    }

    /**
     * Check if the user has all roles.
     *
     * @param int|string|array $role
     * @return bool
     */
    public function isAnd($roles)
    {
        return count((array)$roles) === $this->roles->whereIn('slug', $roles)->count();
    }

    /**
     * Get method name.
     *
     * @param string $methodName
     * @param bool $all
     * @return string
     */
    private function buildMethodName($methodName, $useAnd = false)
    {
        return $methodName . ( (bool)$useAnd ? 'And' : 'Or' ) ;
    }

    /**
     * Check if the user has a permission.
     *
     * @param int|string|array $permission
     * @param bool $all
     * @return bool
     */
    public function can($action, $entity = null)
    {
        return $this->hasRolesForActionOfEntity($action, $entity);
    }

    protected function hasRolesForActionOfEntity($action, $entity = null)
    {
        return !!$this->rolesForActionOfEntity($action, $entity)->count();
    }

    /**
     * Handle dynamic method calls.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'is')) {
            return $this->is(Str::snake(substr($method, 2), ''), $parameters);
        }
        elseif (Str::startsWith($method, 'can')) {
            return $this->can(Str::snake(substr($method, 3), ''), $parameters);

        }

        return parent::__call($method, $parameters);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function (User $user) {
            if(!$user->api_token) $user->api_token = Str::random(60);
        });
    }

}
