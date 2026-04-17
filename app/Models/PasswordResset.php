<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResset extends Model
{
    protected $fillable = [
        'email',
        'code',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
