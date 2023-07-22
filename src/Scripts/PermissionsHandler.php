<?php

namespace LiveControls\Permissions\Scripts;

use App\Models\User;
use Exception;
use LiveControls\Permissions\Models\UserPermission;

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
        if(is_null($user)){
            $user = auth()->user();
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
        }

        return false;
    }
}