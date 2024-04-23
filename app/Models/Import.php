<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $table = 'import';
    protected $fillable = [
        'wh_id',
        'date',
        'salesman',
        'customer',
        'number',
        'type',
        'note',
        'amount',
        'created_user'
    ];
}
