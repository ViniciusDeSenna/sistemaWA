<?php

namespace App\Console\Commands;

use App\Models\DailyRate;
use App\Models\UserHasCompany;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUserCompanyPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user-company-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads the Daily Rate table to find which companies users have registered with, and applies permissions for creating new records in those companies.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $all_daily_rates = DailyRate::all();
        
        $relations_created = 0;

        foreach($all_daily_rates as $daily_rate){
            $userId = $daily_rate->user_id;
            $companyId = $daily_rate->company_id;
            
            $exists = DB::table('user_has_company')
                        ->where('user_id', $userId)
                        ->where('company_id', $companyId)
                        ->exists();

            if (!$exists){
                DB::table('user_has_company')->insert([
                    'user_id' => $userId,
                    'company_id' => $companyId,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $relations_created++;
            }
        }
    }
}
