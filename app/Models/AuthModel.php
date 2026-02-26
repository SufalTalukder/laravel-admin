<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AuthModel extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'auth_tbl';
    protected $primaryKey = 'auth_user_id';

    protected $fillable = [
        'auth_user_email',
        'action_by_user_id',
        'auth_user_name',
        'auth_user_password',
        'auth_user_status',
        'auth_user_phone_number',
        'auth_user_type',
        'auth_user_image',
    ];

    protected $hidden = [
        'auth_user_password',
    ];

    public function getAuthPassword()
    {
        return $this->auth_user_password;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->auth_user_email,
            'role' => $this->auth_user_type
        ];
    }
}
