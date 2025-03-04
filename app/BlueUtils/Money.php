<?php

namespace App\BlueUtils;

class Money
{
    public static function unformat(string $num): float 
    { 
        $num = preg_replace('/[^\d.,]/', '', (string) $num);
        $num = str_replace('.', '', $num);
        $num = str_replace(',', '.', $num);
        $num = floatval($num);

        return $num; 
    }

    public static function format(string $num, string $prefix = "$", int $decimals = 2, ?string $decimal_separator = ".", ?string $thousands_separator = ","): string 
    {
        $num = self::unformat($num);
        $num = number_format($num, $decimals, $decimal_separator, $thousands_separator);
        return $prefix . $num;
    }
}

?>