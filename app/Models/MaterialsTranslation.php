<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialsTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'crayola_name',
        'producer_name',
        'unit'
    ];
}
