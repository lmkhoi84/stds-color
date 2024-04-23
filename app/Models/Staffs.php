<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staffs extends Model
{
    protected $table = 'staffs';
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'citizen_identity_card',
        'birthday',
        'address',
        'area',
        'user_id',
        'status',
        'created_user',
        'updated_user'
    ];
}
