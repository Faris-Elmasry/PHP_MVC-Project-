<?php

namespace App\Models;

use Elmasry\Database\Database;

class User extends Model
{
    protected static $table = 'users';
    protected static $fillable = ['name', 'email', 'password', 'phone', 'address'];
}