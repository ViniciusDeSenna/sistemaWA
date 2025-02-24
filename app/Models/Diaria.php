<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diaria extends Model
{
    protected $fillable = [
        'colaborator_id',
        'company_id',
        'category',
        'start',
        'end',
        'total_time',
        'classification',
    ];
}
