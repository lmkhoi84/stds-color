<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users_Group extends Model
{
    protected $table = 'users_group';
    protected $fillable = [
        'name',
        'status',
        'menus_permission',
        'products_permission',
    ];
}
