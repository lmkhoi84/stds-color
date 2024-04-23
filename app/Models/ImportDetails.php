<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportDetails extends Model
{
    protected $table = 'import_details';
    protected $fillable = [
        'import_id',
        'product_id',
        'wh_id',
        'width',
        'length',
        'add_length',
        'finished_quantity',
        'quantity',
        'actual_quantity',
        'price'
    ];
}
