<?php

namespace App\Console\Commands;

use App\Models\DailyRate;
use App\Models\UserHasCompany;
use Illuminate\Console\Command;

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
    protected $description = 'Sincroniza permissões de usuários com empresas, baseado na tabela daily_rate.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔄 Iniciando sincronização entre colaboradores e empresas com base na tabela daily_rate...");

        $all_daily_rates = DailyRate::all();
        $relations_created = 0;
        $relations_skipped = 0;

        foreach ($all_daily_rates as $daily_rate) {
            $userId = $daily_rate->user_id;
            $companyId = $daily_rate->company_id;

            $relation = UserHasCompany::firstOrCreate(
                [
                    'user_id' => $userId,
                    'company_id' => $companyId,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            if ($relation->wasRecentlyCreated) {
                $relations_created++;
            } else {
                $relations_skipped++;
                // Não exibe info para registros já existentes
            }
        }

        $this->info("\n Sincronização concluída.");
        $this->info("🟩 Relações criadas: {$relations_created}");
        $this->info("🟨 Registros já existentes ignorados: {$relations_skipped}");
    }
}
