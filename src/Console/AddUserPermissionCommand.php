<?php

namespace LiveControls\Permissions\Console;

use LiveControls\Permissions\Models\UserPermission;
use Illuminate\Console\Command;

class AddUserPermissionCommand extends Command
{
    protected $signature = 'livecontrols:addpermission';

    protected $description = 'Adds a new UserPermission to the database';

    public function handle()
    {
        $this->info("Please enter the informations for the new permission.");
        $name = $this->ask('Name');
        while($name == null || $name == ''){
            $this->warn('Permission name is required!');
            $name = $this->ask('Name');
        }
        $key = $this->ask('Key');
        if(UserPermission::where('key', '=', $key)->exists()){
            $key = null;
        }
        while($key == null || $key == ''){
            $this->warn('Permission key is required and needs to be unique!');
            $key = $this->ask('Key');
        }
        $desc = $this->ask('Description (Optional)');

        $this->info('');
        $this->info('------------------------------------');
        $this->info('');
        $this->info('Name: '.$name);
        $this->info('Key: '.$key);
        $this->info('Description: '.$desc);
        if($this->confirm("Are those informations correct?")){
            $group = UserPermission::create([
                'name' => $name,
                'key' => $key,
                'description' => $desc
            ]);
    
            if(is_null($group)){
                $this->warn('Couldnt create User Permission!');
                return;
            }
            $this->info('User Permission created!');
        }
    }
}