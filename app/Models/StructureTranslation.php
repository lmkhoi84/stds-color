<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StructureTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'structure_name',
        'trans_page'
    ];
}
