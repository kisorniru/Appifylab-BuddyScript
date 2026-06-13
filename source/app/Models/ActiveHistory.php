<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveHistory extends Model
{
    protected $fillable = [
        'user_id',
        'os_version',
        'os_type',
        'app_version',
        'app_version_code',
        'device_name',
        'created_at',
        'updated_at',
    ];
}
