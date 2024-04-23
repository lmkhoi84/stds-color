<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    protected $table = 'output';
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
