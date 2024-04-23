<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Materials extends Model
{
    use Translatable;
    protected $table = 'materials';
    protected $fillable = [
        'crayola_code',
        'producer_code',
        'type',
        'status',
        'created_user'
    ];
    public $translatedAttributes = ['crayola_name','producer_name','unit'];
}
