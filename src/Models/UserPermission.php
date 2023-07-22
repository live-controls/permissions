<?php

namespace LiveControls\Permissions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserPermission extends Model{
    use HasFactory;

    protected $table = 'livecontrols_user_permissions';
    
    protected $fillable = [
        'name',
        'key',
        'description'
    ];
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'livecontrols_user_userpermissions', 'user_id', 'user_permission_id');
    }
}