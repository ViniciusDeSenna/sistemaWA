<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        "name",
    ];
    public $timestamps = false;
    public static function getAll()
    {
        return self::all();
    }
}
