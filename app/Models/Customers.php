<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'address',
        'tax_code',
        'contact_name',
        'contact_phone',
        'contact_email',
        'consignee_name',
        'consignee_phone',
        'delivery_address',
        'created_user',
        'updated_user'
    ];
}
