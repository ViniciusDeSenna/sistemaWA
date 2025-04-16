<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CostCategory;

class Cost extends Model
{
    protected $fillable = [
        'category_id',
        'date',
        'value',
        'description',
    ];

    function category()
    {
        return $this->belongsTo(CostCategory::class);
    }
}
