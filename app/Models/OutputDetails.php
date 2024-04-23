<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutputDetails extends Model
{
    protected $table = 'output_details';
    protected $fillable = [
        'output_id',
        'product_id',
        'material_id',
        'quantity',
        'price'
    ];
}
