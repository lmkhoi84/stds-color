<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    protected $table = 'input';
    protected $fillable = [
        'date',
        'staff_id',
        'customer_id',
        'number',
        'type',
        'note',
        'currency',
        'amount',
        'created_user',
        'updated_user'
    ];
}
