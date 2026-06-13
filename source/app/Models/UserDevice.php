<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = [
        'token',
        'device_name',
        'os_version',
        'os_type',
        'created_at',
        'updated_at',
    ];
}
