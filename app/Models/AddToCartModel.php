<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddToCartModel extends Model
{
    protected $table = 'add_to_cart_tbl';
    protected $primaryKey = 'add_to_cart_id';

    protected $fillable = [
        'event_id',
        'auth_user_id',
        'user_id',
        'product_id',
        'add_to_cart_status',
        'quantity',
        'each_product_total_price',
        'created_at',
        'updated_at',
    ];
}
