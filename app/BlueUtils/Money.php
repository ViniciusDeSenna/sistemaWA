<?php

namespace App\BlueUtils;

class Money
{
    public static function unformat(string $num): float 
    {
        // Remove tudo que não for número, ponto ou vírgula
        $num = preg_replace('/[^\d.,]/', '', (string) $num);

        // Se houver mais de uma vírgula, ou seja, número no formato "1.234,56", removemos os pontos
        if (substr_count($num, ',') > 0) {
            $num = str_replace('.', '', $num);
            $num = str_replace(',', '.', $num);
        }

        return floatval($num);
    }


    public static function format(string $num, string $prefix = "$", int $decimals = 2, ?string $decimal_separator = ".", ?string $thousands_separator = ","): string 
    {
        $num = self::unformat($num);
        $num = number_format($num, $decimals, $decimal_separator, $thousands_separator);
        return $prefix . $num;
    }
}

?>