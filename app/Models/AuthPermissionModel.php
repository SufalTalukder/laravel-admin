<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthPermissionModel extends Model
{
    protected $table      = 'auth_permission_tbl';
    protected $primaryKey = 'auth_permission_id';

    protected $fillable = [
        'auth_user_id',
        'add_permission',
        'edit_permission',
        'view_all_permission',
        'view_permission',
        'delete_permission',
        'action_by_user_id',
    ];
}
