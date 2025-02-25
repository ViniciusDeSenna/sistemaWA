<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $fillable = [
        'name',
        'document',
        'observation',
        'time_value',
    ];
    public static function getAll()
    {
        return self::all();
    }
    public static function getActive()
    {
        //eturn self::all();

    }
}
