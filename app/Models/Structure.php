<?php

namespace App\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    use Translatable;
    protected $table = 'structure';
    protected $fillable = [
        'menu_url',
        'parent_id',
        'page_type',
        'sort',
        'level',
        'status',
        'icon',
        'created_user'
    ];

    public $translatedAttributes = ['structure_name','trans_page'];
}
