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
        'collaborator_recieve_cost_id',
    ];

    public function collaborator()
    {
        return $this->belongsTo(Collaborator::class, 'collaborator_recieve_cost_id');
    }
    
    function category()
    {
        return $this->belongsTo(CostCategory::class);
    }
}
