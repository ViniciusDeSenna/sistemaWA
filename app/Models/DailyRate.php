<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRate extends Model
{
    protected $table = 'daily_rate';
    
    protected $fillable = [
        'id',
        'collaborator_id',
        'company_id',
        'start',
        'start_interval',
        'end_interval',
        'end',
        'daily_total_time',
        'hourly_rate',
        'costs',
        'costs_description',
        'addition',
        'addition_description',
        'total',
        'pix_key',
        'observation',
        'active',
        'user_id',
    ];

    public static function getActive()
    {
        return self::query()->where('active', '=', true)->get();
    }

}
