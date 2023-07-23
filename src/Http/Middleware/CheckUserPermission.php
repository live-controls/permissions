<?php

namespace LiveControls\Permissions\Http\Middleware;

use Closure;
use LiveControls\Permissions\Exceptions\InvalidUserPermissionException;
use LiveControls\Permissions\Models\UserPermission;
use Illuminate\Http\Request;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string ...$keys)
    {
        foreach($keys as $key){
            $permission = UserPermission::where('key', '=', $key)->first();
            if(is_null($permission)){
                throw new InvalidUserPermissionException($key);
            }
            
            if($permission->users()->where('user_id', '=', auth()->id())->exists()){
                return $next($request);
            }

            //Check user group permissions
            if(class_exists('\LiveControls\Groups\GroupsServiceProvider', false)){
                if($permission->groups()->whereIn('group_id', auth()->user()->groups()->get()->toArray())->count() > 0){
                    return $next($request);
                }
            }
        }

        abort(403);
    }
}
