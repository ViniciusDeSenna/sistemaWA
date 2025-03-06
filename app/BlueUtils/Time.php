<?php

namespace App\BlueUtils;

class Time
{
    public static function convertTimeToDecimal($time) {        
        list($hours, $minutes) = explode(':', $time);

        // Converte os minutos para fração de hora
        $decimalHours = $hours + ($minutes / 60);
        return (float) number_format($decimalHours, 2, ',', '');
    }
}

?>

