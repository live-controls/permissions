<?php

namespace LiveControls\Permissions;

use LiveControls\Permissions\Console\UserPermissions\RemoveUserFromPermissionCommand;
use LiveControls\Permissions\Console\UserPermissions\AddUserPermissionCommand;
use LiveControls\Permissions\Console\UserPermissions\AddUserToPermissionCommand;
use LiveControls\Permissions\Http\Middleware\CheckUserPermission;
use LiveControls\Permissions\Scripts\PermissionsHandler;
use Illuminate\Support\ServiceProvider;

class PermissionsServiceProvider extends ServiceProvider
{
  public function register()
  {
    //Add Middlewares
    app('router')->aliasMiddleware('userpermission', CheckUserPermission::class);

    $this->app->bind('permissionshandler', function($app){
      return new PermissionsHandler();
    });
  }

  public function boot()
  {
    $migrationsPath = __DIR__.'/../database/migrations/';
    $migrationPaths = [
      $migrationsPath.'userpermissions',
    ];

    $this->loadMigrationsFrom($migrationPaths);

    if ($this->app->runningInConsole())
    {
      $this->commands([
        //User Permission Commands
        AddUserPermissionCommand::class,
        AddUserToPermissionCommand::class,
        RemoveUserFromPermissionCommand::class
      ]);
    }
  }
}
