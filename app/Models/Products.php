<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Products extends Model
{
    use Translatable;
    protected $table = 'products';
    protected $fillable = [
        'code',
        'status',
        'items',
        'formula',
        'created_user',
        'updated_user'
    ];
    public $translatedAttributes = ['name','unit'];
}
