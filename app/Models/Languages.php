<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Languages extends Model
{
    use Translatable;
    protected $table = 'languages';
    protected $fillable = [
        'name',
        'status',
        'sort'
    ];

    public $translatedAttributes = ['languages_id','languages_name'];
}
