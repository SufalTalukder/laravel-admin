<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'user_tbl';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'full_name',
        'email_address',
        'phone_number',
        'dob',
        'user_address',
        'user_type',
        'user_referral_code',
        'user_image',
        'active',
        'created_at',
        'updated_at'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'user_id'       => $this->user_id,
            'phone_number'  => $this->phone_number,
            'user_type'     => $this->user_type
        ];
    }
}
