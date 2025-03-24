<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRate extends Model
{
    protected $table = 'daily_rate';
    
    protected $fillable = [
        'id',
        'collaborator_id',
        'section_id',
        'company_id',
        
        'start',
        'end',
        'total_time',

        'transportation',
        'feeding',
        'addition',
        'pay_amount',
        'leader_comission',
        'earned',
        'profit',
        'inss_paid',
        'tax_paid',
        
        'active',
        'user_id',
    ];
    public static function getActive()
    {
        return self::query()->where('active', '=', true)->get();
    }

}
