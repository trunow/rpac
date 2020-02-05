<?php
namespace Trunow\Rpac\Policies;

use Illuminate\Foundation\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

class RpPolicy {

    use HandlesAuthorization;

    protected $scopes = []; //['owner'];

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
        foreach($this->scopes as $scope) {
            $scopeActionReturn = ($scopeForActionMethod = $this->checkScopeForAction($scope, $action))
                ? $this->$scopeForActionMethod($user, $entity)
                : null;
            if(is_bool($scopeActionReturn)) return $scopeActionReturn;

            // TODO [*__summary__] ??? need it config ??? return summary AND/OR for all
            $scopeReturn = ($scopeMethod = $this->checkScope($scope))
                ? $this->$scopeMethod($user, $action, $entity)
                : null;
            if(is_bool($scopeReturn)) return $scopeReturn;
        }
    }

    private function checkScopeForAction($scope = '', $action){
        $methodName = ($action && $scope && is_string($scope)) ? 'scoped' . ucfirst($action) . 'For' . ucfirst(Str::camel($scope)) : null;

        return method_exists($this, $methodName)
            ? $methodName
            : null;
    }

    private function checkScope($scope = ''){
        $methodName = ($scope && is_string($scope)) ? 'scopeFor' . ucfirst(Str::camel($scope)) : null;

        return method_exists($this, $methodName)
            ? $methodName
            : null;
    }

    /**
     * Magic  for all policy's methods
     *
     * @param $action
     * @param $parameters
     * @return bool|null
     */
    public function __call($action, $parameters)
    {
        if(!$action) return false;

        $user = $parameters[0] ?? auth()->user();
        $entity = $parameters[1] ?? $this->getEntityClass();

        // TODO get mapAbilities ... and >>> if(!method_exists($entity, $action)) return false;

        $before = $this->beforeCanForUser($user, $action, $entity);

        if(is_bool($before)) return $before;

        return $user->can($action, $entity);
    }


    /*
     *
     *         E X A M P L E S     S C O P E S
     *
     */
    protected function scopedViewAnyForOwner(?User $user, $entity) {
        $entity::addGlobalScope('owner', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        });
    }
    protected function scopedViewForOwner(?User $user, $entity) {
        return $user->id === $entity->user_id;
    }
    ////
    protected function scopeForSuperUser(?User $user, $action, $entity)
    {
        return $user->isSu();
    }
    protected function scopeForGuest(?User $user, $action, $entity)
    {
        return !$user; //(new User)->isGuest($method, $entity);
    }
    protected function scopeForTrue(?User $user, $action, $entity)
    {
        return true;
    }
    protected function scopeForFalse(?User $user, $action, $entity)
    {
        return false;
    }
    protected function scopeForOwner(?User $user, $action, $entity)
    {
        $entity::addGlobalScope('owner', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        });
        //::addGlobalScope(new OwnerScope);
    }
}
