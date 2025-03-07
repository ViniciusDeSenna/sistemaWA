<?php

namespace App\BlueUtils;

class Number
{
    public static function onlyNumber(string $num): string 
    { 
        return preg_replace('/\D/', '', $num);
    }
}

?>