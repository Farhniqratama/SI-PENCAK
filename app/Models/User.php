<?php

namespace App\Models;

use App\Models\Concerns\CodeIgniterModelCompatibility;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use CodeIgniterModelCompatibility;

    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
