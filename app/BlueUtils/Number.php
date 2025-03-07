<?php

namespace App\BlueUtils;

class Number
{
    public static function onlyNumber(string $num): int 
    { 
        return intval(preg_replace('/\D/', '', $num));
    }
}

?>