<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportDetails extends Model
{
    protected $table = 'export_details';
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
