<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigTable extends Model
{
    protected $fillable = [
        "value",
    ];
    protected $table = "config_table";

    public static function getValue($id){
        return ConfigTable::where('id', $id)->first()->value;
    }
}
