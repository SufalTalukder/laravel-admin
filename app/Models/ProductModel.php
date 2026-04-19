<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    protected $table = 'product_tbl';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'language_id',
        'product_name',
        'product_slug',
        'product_brand',
        'product_code',
        'bound_type',
        'book_size',
        'best_seller',
        'new_arrival',
        'product_availability',
        'product_price',
        'product_details',
        'product_image',
        'product_stock',
        'status'
    ];
}
