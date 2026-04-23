<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddToWishlistModel extends Model
{
    protected $table = 'add_to_favourite_tbl';
    protected $primaryKey = 'add_to_favourite_id';

    protected $fillable = [
        'event_id',
        'auth_user_id',
        'product_id',
        'user_id',
        'add_to_wishlist_status',
        'created_at',
        'updated_at',
    ];
}
