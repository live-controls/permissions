<?php

namespace LiveControls\Permissions\Scripts;

use App\Models\User;
use Exception;
use LiveControls\Permissions\Models\UserPermission;
use LiveControls\Groups\Models\UserGroup;

class PermissionsHandler{
    private array $permissions = [];

    public function add(string ...$permissions){
        array_push($this->permissions, $permissions);

        return $this;
    }

    public function addArray(array $permissions){
        array_push($this->permissions, $permissions);

        return $this;
    }

    public function remove(string $permission){
        $updatedPermissions = [];
        foreach($this->permissions as $setPermission){
            if($setPermission != $permission){
                array_push($updatedPermissions, $permission);
            }
        }
        $this->permissions = $updatedPermissions;
        return $this;
    }

    public function clear(){
        $this->permissions = [];
        return $this;
    }

    public function check(User $user = null):bool{
        if(!in_array(HasGroups::class, class_uses_recursive(User::class))){
            throw new Exception('HasGroups trait is missing in User class!');
        }

        if(is_null($user)){
            $user = auth()->user();
        }

        if(class_exists('\LiveControls\Groups\GroupsServiceProvider', false)){
            $groups = $user->groups;
        }else{      
            $groups = null;
        }
        foreach($this->permissions as $permission){
            $perm = UserPermission::where('key', '=', $permission)->first();
            if(is_null($perm)){
                //Ignore if permission was not found
                continue;
            }
            if($perm->users()->where('users.id', '=', $user->id)->exists()){
                return true;
            }
            foreach($groups as $group){
                if($perm->groups()->where('livecontrols_user_groups.id', '=', $group->id)->exists()){
                    return true;
                }
            }
        }

        return false;
    }

    public function checkGroup(UserGroup $group):bool{
        foreach($this->permissions as $permission){
            $perm = UserPermission::where('key', '=', $permission)->first();
            if(is_null($perm)){
                //Ignore if permission was not found
                continue;
            }
            if($perm->groups()->where('user_groups.id', '=', $group->id)->exists()){
                return true;
            }
        }
        return false;
    }
}