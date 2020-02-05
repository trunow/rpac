<?php
namespace Trunow\Rpac\Policies;

use Illuminate\Foundation\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

//use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RpPolicy {

    use HandlesAuthorization;//, AuthorizesRequests;

    protected $_scopes = []; //['su'];

    protected $entityName;

    public function __construct()
    {
        $policyPath = get_class($this);
        $this->entityName = substr($policyPath, 0, strpos($policyPath, '\\') + 1) . substr(class_basename($this), 0, -6);
    }

    protected function getEntityClass()
    {
        return new $this->entityName;
    }

    protected function beforeCanForUser(?User $user, $action, $entity)
    {
        // EXAMPLE scopeForIsGuest(...) { if(!$user) return ['return' => (new User)->isGuest($method, $entity)];}

        foreach($this->_scopes as $scope) {
            $scopeMethod = $this->checkScope($scope);

            if($scopeMethod) return $this->$scopeMethod($user, $action, $entity);
        }

    }

    protected function canForUser(?User $user, $action, $entity)
    {
        $before = $this->beforeCanForUser($user, $action, $entity);
        if($before && is_array($before) && key_exists('return', $before)) return $before['return'];

        $can = $user->can($action, $entity);

        return $can;
    }

    public function __call($action, $parameters)
    {
        if(!$action) return false;

        $user = $parameters[0] ?? auth()->user();
        $entity = $parameters[1] ?? $this->getEntityClass();

        // TODO get mapAbilities ... and >>> if(!method_exists($entity, $action)) return false;

        return $this->canForUser($user, $action, $entity);
    }


    private function checkScope($scope = ''){
        $methodName = ($scope && is_string($scope)) ? 'scopeFor' . Str::camel($scope) : null;

        return method_exists($this, $methodName)
            ? $methodName
            : null;
    }

    /*
     *
     *         E X A M P L E S     S C O P E S
     *
     */

    protected function scopeForSu(?User $user, $action, $entity)
    {
        return ['return' => $user->isSu()];
    }
    protected function scopeForTrue(?User $user, $action, $entity)
    {
        return ['return' => true];
    }
    protected function scopeForFalse(?User $user, $action, $entity)
    {
        return ['return' => false];
    }
    protected function scopeForOwner(?User $user, $action, $entity)
    {
        $entity::addGlobalScope('owner', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        });
        //::addGlobalScope(new OwnerScope);
    }
}
