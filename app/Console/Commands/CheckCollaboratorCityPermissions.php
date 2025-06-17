<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Company;
use App\Models\CompanyHasCity;
use App\Models\DailyRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckCollaboratorCityPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-collaborator-city-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all DailyRate records to create Collaborator-City relations based on the Company\'s city.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando sincronização entre colaboradores e cidades com base na tabela daily_rate...");

        $all_daily_rates = DailyRate::all();
        $relations_created = 0;
        $relations_skipped = 0;

        foreach ($all_daily_rates as $daily_rate) {
            $collaboratorId = $daily_rate->collaborator_id;

            // Busca a relação entre empresa e cidade
            $companyCity = CompanyHasCity::where('company_id', $daily_rate->company_id)->first();

            if (!$companyCity || !$companyCity->city_id) {
                $this->warn("Relação empresa-cidade não encontrada: company_id {$daily_rate->company_id}");
                $relations_skipped++;
                continue;
            }

            $cityId = $companyCity->city_id;

            // Cria ou atualiza a relação colaborador-cidade
            DB::table('city_has_collaborator')->updateOrInsert(
                [
                    'collaborator_id' => $collaboratorId,
                    'city_id' => $cityId,
                ],
                [
                    'active' => true,
                    'updated_at' => now(),
                    'created_at' => now(), // será ignorado se já existir
                ]
            );

            $relations_created++;
        }

        $this->info("\nSincronização concluída.");
        $this->info("Relações criadas ou atualizadas: {$relations_created}");
        $this->info("Registros ignorados (sem cidade): {$relations_skipped}");
    }

}
