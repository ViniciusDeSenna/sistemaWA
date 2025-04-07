<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    protected $table = "costs";
    protected $fillable = [
        'category_id',
        'date',
        'value',
        'description',
    ];
}
