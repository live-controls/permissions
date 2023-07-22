<?php

namespace LiveControls\Permissions\Console;

use LiveControls\Permissions\Models\UserPermission;
use Illuminate\Console\Command;
use App\Models\User;

class RemoveUserFromPermissionCommand extends Command
{
    protected $signature = 'livecontrols:unsetpermission';

    protected $description = 'Removes an user from an userpermission';

    public function handle()
    {
        //IS USER
        $id = $this->ask('User ID');
        $permissionKey = $this->ask('Permission Key');

        $user = User::find($id);
        if(is_null($user)){
            $this->warn('Invalid User ID!');
            return;
        }

        $group = UserPermission::where('key', '=', $permissionKey)->first();

        if(is_null($group)){
            $this->warn('Invalid Permission Key!');
            return;
        }

        if(!$group->users()->where('users.id', '=', $id)->exists()){
            $this->warn('User does not have this permission!');
            return;
        }
        
        $group->users()->detach($id);
        $this->info('Removed user from permission!');
    }
}