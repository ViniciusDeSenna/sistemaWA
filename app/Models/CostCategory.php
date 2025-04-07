<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCategory extends Model
{
    protected $table = 'cost_categories';
    protected $fillable = [
        'name',
    ];
}
