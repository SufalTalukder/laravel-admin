<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemActivityModel extends Model
{
    protected $table = 'auth_login_audit_tbl';
    protected $primaryKey = 'auth_login_audit_id';

    protected $fillable = [
        'auth_method',
        'browser',
        'created_at',
        'device_type',
        'ip_address',
        'login_status',
        'login_time',
        'operating_system',
        'possible_incognito',
        'user_agent',
        'auth_user_id',
        'browser_version',
        'device_model',
        'failure_reason',
        'os_version',
        'referrer_url',
        'session_id',
        'country',
        'state',
        'city',
        'address',
        'lat',
        'lon',
        'updated_at'
    ];

    protected $casts = [
        'login_time' => 'datetime'
    ];
}
