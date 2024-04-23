<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsDetails extends Model
{
    public $table = 'products_details';
    protected $fillable = [
        'products_id',
        'materials_id',
        'percentage',
        'accuracy',
        'created_user',
        'updated_user'
    ];
}
