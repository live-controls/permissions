<?php

namespace LiveControls\Permissions\Console;

use Illuminate\Console\Command;
use App\Models\User;
use LiveControls\Permissions\Models\UserPermission;

class AddUserToPermissionCommand extends Command
{
    protected $signature = 'livecontrols:setpermission';

    protected $description = 'Adds an user to an userpermission';

    public function handle()
    {
        //IS USER
        $id = $this->ask('User ID');
        $permissionkey = $this->ask('Permission Key');

        $user = User::find($id);
        $permission = UserPermission::where('key', '=', $permissionkey)->first();

        if(is_null($permission)){
            $this->warn('Invalid Permission Key!');
            return;
        }
        if(is_null($user)){
            $this->warn('Invalid User ID!');
            return;
        }

        if($permission->users()->where('users.id', '=', $id)->exists()){
            $this->warn('User already has permission!');
            return;
        }

        $permission->users()->attach($id);

        $this->info('Added user to permission!');
    }
}