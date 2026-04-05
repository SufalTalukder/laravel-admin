<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddressModel extends Model
{
    protected $table = 'user_address_tbl';
    protected $primaryKey = 'user_address_id';

    protected $fillable = [
        'user_id',
        'address_type',
        'user_address',
        'user_city',
        'user_state',
        'user_country',
        'user_pincode',
        'created_at',
        'updated_at'
    ];
}
