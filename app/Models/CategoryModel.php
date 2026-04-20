<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = 'category_tbl';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'event_id',
        'auth_user_id',
        'category_name',
        'category_slug',
        'category_image',
        'category_status',
        'created_at',
        'updated_at'
    ];
}
