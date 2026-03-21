<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpModel extends Model
{
    protected $table      = 'otp_tbl';
    protected $primaryKey = 'otp_id';

    protected $fillable = [
        'user_id',
        'phone_number',
        'otp',
        'otp_verified',
        'otp_expired',
    ];

    protected $casts = [
        'otp_verified' => 'boolean',
        'otp_expired'  => 'datetime',
    ];

    public static function getValidOtp($userId, $otp)
    {
        return self::where('user_id', $userId)
            ->where('otp', $otp)
            ->where('otp_verified', 0)
            ->where('otp_expired', '>', now())
            ->latest()
            ->first();
    }
}
