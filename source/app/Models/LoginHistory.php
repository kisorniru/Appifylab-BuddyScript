<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'os_version',
        'os_type',
        'device_name',
        'app_version',
        'app_version_code',
        'created_at',
        'updated_at',
    ];
}
