<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $table = 'export';
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
