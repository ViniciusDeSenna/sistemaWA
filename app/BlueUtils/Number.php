<?php

namespace App\BlueUtils;

class Money
{
    public static function onlyNumber(string $num): int 
    { 
        return intval(preg_replace('/\D/', '', $num));
    }
}

?>