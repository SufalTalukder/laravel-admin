<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentlyViewedProductModel extends Model
{
    protected $table = 'recently_viewed_tbl';
    protected $primaryKey = 'recently_viewed_id';

    protected $fillable = [
        'event_id',
        'auth_user_id',
        'user_id',
        'product_id',
        'view_status',
        'created_at',
        'updated_at',
    ];
}
