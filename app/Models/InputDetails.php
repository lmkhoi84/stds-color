<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputDetails extends Model
{
    protected $table = 'input_details';
    protected $fillable = [
        'input_id',
        'material_id',
        'quantity',
        'price'
    ];
}
