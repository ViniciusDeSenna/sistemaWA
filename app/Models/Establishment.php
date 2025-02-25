<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    protected $fillable = [
        'name',
        'document',
        'observation',
        'time_value',
    ];

    public static function getActive()
    {
        return self::query()->get();

    }
}
