<?php

namespace Trunow\Rpac\Policies;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;
use Trunow\Rpac\Permission;
use Trunow\Rpac\Role;
use Trunow\Rpac\Traits\PlayingRoles;

abstract class GenericPolicy
{
    use HandlesAuthorization;

    /**
     * Set of relationships between User and Model
     * @var array
     * @example ['owner', 'manager']
     */
    protected $relationships = [];

    /**
     * Per-hit data storage
     * @var array
     */
    private $cache = [];

    /**
     * Returns list of available Policy actions
     * @param string $option {model => returns actions for model; non-model => returns actions for non-model}
     * @return array
     * @throws \ReflectionException
     * @deprecated Move it to admin backend
     */
    public function actions($option = null)
    {
        $pseudoName = $this->pseudoName();
        $reflection = new \ReflectionClass(get_class($this));
        return array_values(
            array_map(function (\ReflectionMethod $n) use ($pseudoName) {
                if ($n->class != __CLASS__) {
                    // Need to add namespace
                    return $pseudoName . '\\' . $n->name;
                } else {
                    // BuiltIn actions
                    return $n->name;
                }
            }, array_filter($reflection->getMethods(), function (\ReflectionMethod $n) use ($option) {
                    if ($n->isPublic()) {

                        /* @var \ReflectionParameter $userParam */
                        /* @var \ReflectionParameter $modelParam */
                        $userParam = @$n->getParameters()[0];
                        $modelParam = @$n->getParameters()[1];

                        if ($option != 'non-model') {
                            // Require both parameters
                            if ($userParam && $modelParam && $userParam->name == 'user' && $modelParam->name == 'model') {
                                return $n;
                            }
                        }

                        if ($option != 'model') {
                            // Require only user parameter
                            if ($userParam && !$modelParam && $userParam->name == 'user') {
                                return $n;
                            }
                        }
                    }
                    return null;
                })
            )
        );
    }

    /**
     * Returns list of available Policy relationships
     * @param boolean $namespace apply namespace
     * @return array
     * @throws \ReflectionException
     * @deprecated Move it to admin backend
     */
    public function relationships($namespace = false)
    {
        $reflection = new \ReflectionClass($this->model());
        $methods = array_filter($reflection->getMethods(), function (\ReflectionMethod $n) {
            return Str::startsWith($n->name, 'scopeRelationship') ? $n : null;
        });
        $methods = array_map(function (\ReflectionMethod $n) {
            $name = Str::replaceFirst('scopeRelationship', '', $n->name);
            return Str::snake($name);
        }, $methods);

        $methods = $namespace ? $this->applyNamespace($this->pseudoName(), $methods) : $methods;

        return array_merge($namespace ? $this->applyNamespace('Core', ['guest', 'any']) : ['guest', 'any'], $methods);
    }

    /**
     * Returns all available Roles and Relationships applicable to the Model
     * @return array
     * @deprecated Move it to admin backend
     */
    public function rolesAndRelationships()
    {
        $roles = array_merge(
            $this->applyNamespace('Core', ['guest', 'any']),
            $this->applyNamespace('Role', Role::pluck('slug')->toArray()),
            $this->applyNamespace($this->pseudoName(), $this->relationships)
        );

        return $roles;
    }


    /**
     * Prepend namespace to string or array of strings
     * @param $namespace
     * @param $data
     * @return array|string
     */
    private function applyNamespace($namespace, $data)
    {
        if (is_array($data)) {
            return array_map(function ($n) use ($namespace) {
                return $this->applyNamespace($namespace, $n);
            }, $data);
        } else {
            return $namespace . '\\' . Str::studly($data);
        }
    }

    /**
     * The Policy pseudo-name for usage in permission table
     * @return string
     */
    private function pseudoName()
    {
        $name = explode('Policies\\', get_class($this));
        $name = Str::replaceFirst('Policy', '', $name[1]);

        return $name;
    }

    /**
     * Policy need to know Model it works with
     * @return string
     */
    abstract protected function model();

    public function viewAny(?User $user)
    {
        return $this->authorizeAndScope('viewAny', 'view', $user);
    }

    public function create(?User $user)
    {
        return $this->authorize('create', $user);
    }

    public function view(?User $user, Model $model)
    {
        return $this->authorize('view', $user, $model);
    }

    public function update(?User $user, Model $model)
    {
        return $this->authorize('update', $user, $model);
    }

    public function delete(?User $user, Model $model)
    {
        return $this->authorize('delete', $user, $model);
    }

    private function getScopeName($relationship)
    {
        $scopeName = 'relationship' . Str::studly($relationship); // relationship{Name}()
        $methodName = 'scope' . Str::studly($scopeName); // scopeRelationship{Name}()
        return $scopeName;
//        return method_exists($this->model(), $methodName) ? $scopeName : null;
    }

    /**
     * @param $action
     * @param User|null $user
     * @return $this
     */
    private function scope($action, ?User $user)
    {
        $scopeName = "{$this->model()}\\{$action}";
        $model = $this->model();
        /* @var Model $ent */
        $ent = new $model();

        // As authorize() called without model, it checks only roles
        // If it returns true — user may observe all recordSet

        if (!$this->authorize($action, $user)) {

            // Get all user relationship scopes and combine them into one query
            // Then scope model with that query
            // If model has no relationship scopes, will return empty scope

            if ($relationships = $this->getActionRelationships($action)) {
                $this->model()::addGlobalScope($scopeName, function (Builder $query) use ($relationships, $user) {
                    foreach ($relationships as $relationship) {

                        $scopeName = $this->getScopeName($relationship);
                        $query->orWhere(function (Builder $query) use ($scopeName, $user) {
                            $query->$scopeName($user);
                        });

                    }
                });
            } else {
                // Developer not defined any relationship scopes
                // Apply empty scope to prevent user access to unauthorized models
                $this->model()::addGlobalScope($scopeName, function (Builder $query) use ($ent) {
                    $query->where($ent->getKeyName(), 0);
                });
            }

        } else {
            // User has total access to whole recordSet
        }
    }

    /**
     * Returns QueryBuilder assosiated to relationship
     * @param string $relationship
     * @param User $user
     * @return Builder|null
     */
    private function relationshipQuery($relationship, ?User $user)
    {
        if ($scopeName = $this->getScopeName($relationship)) {
            return $this->model()::$scopeName($user);
        } else {
            return null;
        }
    }

    /**
     * Returns concrete user roles
     * @param User|PlayingRoles|null $user
     * @return array
     */
    protected function getUserRoles(?User $user)
    {
        if ($user) {
            if (!isset($this->cache["user-roles"])) {
                $this->cache["user-roles"] =
                    $user->roles->pluck('slug')->toArray();
//                    $this->applyNamespace('Role', $user->roles->pluck('slug')->toArray())
            }
            return $this->cache["user-roles"];
        } else {
            return [];
        }
    }

    /**
     * Returns set of relationships between current Model and current User
     * @param User|null $user
     * @param $model
     * @return array
     */
    protected function getUserRelationships(?User $user = null, ?Model $model = null)
    {
        if ($user) {
            $roles = $this->applyNamespace('Core', ['any']);

            if ($model) {
                foreach ($this->relationships as $relationship) {
                    if ($query = $this->relationshipQuery($relationship, $user)) {
                        // Check if given Model relates to User throw $relationship
                        $query->where($model->getKeyName(), $model->getKey());
                        if ($query->count()) {
                            $roles[] = $this->applyNamespace($this->pseudoName(), $relationship);
                        }
                    }
                }
            }

        } else {
            $roles = $this->applyNamespace('Core', ['guest']);
        }

        return $roles;
    }

    /**
     * Returns relationships, allowed to perform given action
     * @param string $action
     * @return array
     */
    protected function getActionRelationships($action)
    {
        $entity = $this->pseudoName();

        $relationships = Permission::cached()->filter(
            function (Permission $perm) use ($action, $entity) {
                return (
                    $perm->action == $action &&
                    $perm->entity == $entity
                );
            }
        )->pluck('role')->toArray();

        $relationships = array_map(function ($n) {
            $n = explode('\\', $n);
            $n = array_pop($n);
            return Str::snake($n);
        }, $relationships);

        $relationships = array_intersect($relationships, $this->relationships);

        return $relationships;
    }

    /**
     * Checks User ability to perform Action against Model and put scope on Model
     * @param string $action action to check
     * @param string $scope make scope with only Models allowed to that action
     * @param User|null $user
     * @return bool
     */
    protected function authorizeAndScope($action, $scope, ?User $user)
    {
        $allow = $this->authorize($action, $user);
        $this->scope($scope, $user);
        return $allow;
    }

    /**
     * Checks User ability to perform Action against Model
     * @param string $action
     * @param User|null $user
     * @param Model|null $model
     * @return bool
     */
    protected function authorize($action, ?User $user, $model = null)
    {
        $roles = array_merge(
            $this->getUserRelationships($user, $model),
            $this->getUserRoles($user)
        );
        $entity = $this->pseudoName();

//        dump("User: {$user}");
//        dump("Action: {$action}");
//        dump("Against: {$model}");
//        dump(array_merge($roles));

        $permissions = Permission::cached()->filter(
            function (Permission $perm) use ($action, $entity, $roles) {
                return (
                    $perm->action == $action &&
                    $perm->entity == $entity &&
                    in_array($perm->role, $roles)
                );
            }
        );

        return (boolean)$permissions->count();
    }
}
