<?php

namespace LiveControls\Permissions\Traits;

use Exception;
use LiveControls\Permissions\Models\UserPermission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Session;
use App\Models\User;

trait HasPermissions{
    public function permissions(): BelongsToMany
    {
        if(!isset($this->permissionsTable)){
            $this->permissionsTable = 'livecontrols_user_userpermissions';
        }
        if(!isset($this->permissionsForeignColumn))
        {
            $this->permissionsForeignColumn = 'user_id';
        }
        return $this->belongsToMany(UserPermission::class, $this->permissionsTable, $this->permissionsForeignColumn, 'user_permission_id');
    }

    public function hasNotPermission(string $key): bool
    {
        foreach($this->permissions as $permission){
            if($permission->key == $key){
                return false;
            }
        }
        return true;
    }

    public function hasNotOnePermission(array $keys): bool
    {
        foreach($keys as $key){
            if($this->hasPermission($key)){
                return false;
            }
        }
        return true;
    }

    public function hasNotPermissions(array $keys): bool
    {
        foreach($keys as $key){
            if($this->hasPermission($key)){
                return false;
            }
        }
        return true;
    }

    public function hasPermission(string $key): bool
    {
        foreach($this->fetchPermissions() as $permission){
            if($permission->key == $key){
                return true;
            }
        }
        return false;
    }

    public function hasOnePermission(array $keys): bool
    {
        foreach($keys as $key){
            if($this->hasPermission($key)){
                return true;
            }
        }
        return false;
    }

    public function hasPermissions(array $keys): bool
    {
        foreach($keys as $key){
            if(!$this->hasPermission($key)){
                return false;
            }
        }
        return true;
    }

    public function hasAdminPermission(): bool
    {
        //Checks if user has rank admin (Will be ignored if rank does not exist)
        if($this->rank == 'admin'){
            return true;
        }
        //Checks if user is root
        if($this instanceof User){
            if(in_array($this->id, config('livecontrols_permissions.root_users',[]))){
                return true;
            }
        }
        return false;
    }

    public function addPermissions(UserPermission|int|string ...$permissions)
    {
        foreach($permissions as $permission)
        {
            $this->addPermission($permission);
        }
    }

    public function addPermission(UserPermission|int|string $permission)
    {
        if(is_numeric($permission)){
            $permission = UserPermission::find($permission);
        }
        elseif(is_string($permission)){
            $permission = UserPermission::where('key', '=', $permission)->first();
        }
        if(is_null($permission)){
            throw new Exception('Invalid permission!');
        }
        $this->permissions()->attach($permission->id);
        
        //Reload permissions into session
        $this->fetchPermissions(true);
    }

    public function removePermissions(UserPermission|int|string ...$permissions)
    {
        foreach($permissions as $permission)
        {
            $this->removePermission($permission);
        }
    }

    public function removePermission(UserPermission|int|string $permission)
    {
        if(is_numeric($permission)){
            $permission = UserPermission::find($permission);
        }
        elseif(is_string($permission)){
            $permission = UserPermission::where('key', '=', $permission)->first();
        }
        if(is_null($permission)){
            throw new Exception('Invalid permission!');
        }
        $this->permissions()->detach($permission->id);
        
        //Reload permissions into session
        $this->fetchPermissions(true);
    }

    public function togglePermission(UserPermission|int|string $permission)
    {
        if(is_numeric($permission)){
            $permission = UserPermission::find($permission);
        }
        elseif(is_string($permission)){
            $permission = UserPermission::where('key', '=', $permission)->first();
        }
        if(is_null($permission)){
            throw new Exception('Invalid permission!');
        }
        if($this->permissions->contains($permission->id)){
            $this->permissions()->detach($permission->id);
            return;
        }
        $this->permissions()->attach($permission->id);

        //Reload permissions into session
        $this->fetchPermissions(true);
    }

    private function fetchPermissions(bool $reload = false) : \Illuminate\Database\Eloquent\Collection
    {
        if((isset($this->permissionsTable) && $this->permissionsTable == "livecontrols_user_userpermissions") || !isset($this->permissionsTable)){
            $permissions = Session::get('user_permissions', null);
            $ts = Session::get('user_permissions_timestamp', 0);
            if(is_null($permissions) || time() < $ts + (60 * 60) || $reload){
                $permissions = $this->permissions()->get();
                Session::put('user_permissions', $permissions);
                Session::put('user_permissions_timestamp', time());
            }
        }else{
            if(!isset($this->permissionsName))
            {
                throw new Exception('protected $permissionsName is not set, but is needed!');
            }
            $permissions = Session::get($this->permissionsName.'_user_permissions', null);
            $ts = Session::get($this->permissionsName.'user_permissions_timestamp', 0);
            if(is_null($permissions) || time() < $ts + (60 * 60) || $reload){
                $permissions = $this->permissions()->get();
                Session::put($this->permissionsName.'_user_permissions', $permissions);
                Session::put($this->permissionsName.'_user_permissions_timestamp', time());
            }
        }
        return $permissions;
    }
}