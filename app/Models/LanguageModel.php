<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageModel extends Model
{
    protected $table = 'language_tbl';
    protected $primaryKey = 'language_id';

    protected $fillable = [
        'event_id',
        'language_name',
        'language_slug',
        'language_status',
        'language_image',
        'created_at',
        'updated_at'
    ];
}
