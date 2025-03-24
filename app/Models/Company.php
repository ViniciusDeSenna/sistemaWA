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
        'category',
        'city',
        'uniforms_laid',
        'chain_of_stores',
    ];
    public static function getAll()
    {
        return self::all();
    }
    public static function getActive()
    {
        return self::query()->where('active', '=', true)->get();

    }
    
    public function companySections()
    {
        return $this->hasMany(CompanyHasSection::class);
    }
}
