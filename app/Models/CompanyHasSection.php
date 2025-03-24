<?php
namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CompanyHasSection extends Model
{
    public $timestamps = false;

    protected $table = 'company_has_section';
    protected $fillable = [
        "company_id",
        "section_id",

        "pay_amount",
        "leaderPay",
        
        "leaderComission",
        "extra",
        "profit",
        "feeding",
        "earned",
        "perHour",
        "active",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function getActive()
    {
        return self::query()->where('active', '=', true)->get();

    }
    
}
