<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'city';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uId',
        'stateId',
        'name',
        'state'
    ];
}
