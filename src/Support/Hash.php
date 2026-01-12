<?php
namespace Elmasry\Support;

class Hash
{


    public static function password ($value){
        return password_hash($value , PASSWORD_BCRYPT);
    }
    public static function make($value)
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    public static function verify($value, $hashedValue)
    {
        return password_verify($value, $hashedValue);
    }


}