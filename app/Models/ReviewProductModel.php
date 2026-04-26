<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewProductModel extends Model
{
    protected $table = 'user_rating_tbl';
    protected $primaryKey = 'user_rating_id';

    protected $fillable = [
        'event_id',
        'auth_user_id',
        'product_id',
        'user_id',
        'user_rating',
        'user_comment',
        'created_at',
        'updated_at',
    ];
}
