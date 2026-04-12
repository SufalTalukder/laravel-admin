<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategoryModel extends Model
{
    protected $table = 'sub_category_tbl';
    protected $primaryKey = 'sub_category_id';

    protected $fillable = [
        'auth_user_id',
        'sub_category_name',
        'sub_category_slug',
        'sub_category_image',
        'sub_category_status',
        'created_at',
        'updated_at'
    ];
}
