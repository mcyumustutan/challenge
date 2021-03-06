<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Device extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'device';

    protected $fillable = [
        'uid',
        'appId',
        'language',
        'os', 
    ];
}
