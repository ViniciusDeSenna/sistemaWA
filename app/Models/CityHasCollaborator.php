<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityHasCollaborator extends Model
{
    protected $table = 'city_has_collaborator';
    protected $fillable = [
        'collaborator_id',
        'city_id',
        'is_active',
    ];
} 
