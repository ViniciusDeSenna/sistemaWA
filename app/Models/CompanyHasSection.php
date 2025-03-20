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
        "employeePay",
        "leaderPay",
        "leaderComission",
        "earned",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public static function storeOrUpdateSectionArray($sectionData)  
    {

        try {
            $data = [
                'employeePay' => $sectionData['employeePay'],
                'leaderPay' => $sectionData['leaderPay'],
                'leaderComission' => $sectionData['leaderComission'] ?? 0,
                'earned' => $sectionData['earned'],
            ];
    
            $companyHasSection = self::updateOrCreate(
                [
                    'company_id' => $sectionData['company_id'],
                    'section_id' => $sectionData['section_id'],
                ],
                $data
            );
    
            return $companyHasSection;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    public static function storeOrUpdateSectionObject($sectionData)  
    {
        try {    
            $data = [
                'employeePay' => $sectionData->employeePay,
                'leaderPay' => $sectionData->leaderPay,
                'leaderComission' => $sectionData->leaderComission ?? 0,
                'earned' => $sectionData->earned,
            ];
    
            $companyHasSection = self::updateOrCreate(
                [
                    'company_id' => $sectionData->company_id,
                    'section_id' => $sectionData->section_id,
                ],
                $data
            );
    
            return $companyHasSection;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
