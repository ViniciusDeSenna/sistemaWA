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

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function collaborator()
    {
        return $this->belongsTo(Collaborator::class, 'collaborator_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
