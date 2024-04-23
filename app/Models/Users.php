<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'full_name',
        'email',
        'address',
        'password',
        'phone_number',
        'id_card',
        'user_group',
        'default_language',
        'status',
        'profile_picture',
        'last_login',
        'menus_permission',
    ];
}
