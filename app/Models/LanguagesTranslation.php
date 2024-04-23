<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguagesTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'languages_id',
        'languages_name',
    ];
}
