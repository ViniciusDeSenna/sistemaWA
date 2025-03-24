<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRate extends Model
{
    protected $table = 'daily_rate';
    
    protected $fillable = [
        'id',
        'collaborator_id',
        'company_has_section_id',
        'company_id',
        
        'start',
        'end',
        'daily_total_time',

        'transportation',
        'feeding',
        'addition',
        'pay_amount',
        'leader_comission',
        'earned',
        'profit',

        'active',
        'user_id',
    ];
    public static function getActive()
    {
        return self::query()->where('active', '=', true)->get();
    }

}
