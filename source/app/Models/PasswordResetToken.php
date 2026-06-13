<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $primaryKey = 'email';

    public $incrementing = false;

    public const UPDATED_AT = null;

    protected $fillable = [
        'email',
        'token',
        'created_at',
        'expires_at',
    ];
}
