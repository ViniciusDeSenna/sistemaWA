<?php

namespace App\Console\Commands;

use App\Models\Company;
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
    $this->info("Iniciando sincronização entre colaboradores e cidades com base na tabela daily_rate...");

    // Obtém todas as diárias registradas no sistema
    $all_daily_rates = DailyRate::all();
    $relations_created = 0;
    $relations_skipped = 0;

        foreach ($all_daily_rates as $daily_rate) {
            $collaboratorId = $daily_rate->collaborator_id;

            // Busca a empresa associada à diária
            $company = Company::find($daily_rate->company_id);

            if (!$company || !$company->city_id) {
                // Caso a empresa não exista ou não tenha cidade vinculada
                $this->warn("Empresa não encontrada ou sem cidade: ID {$daily_rate->company_id}");
                $relations_skipped++;
                continue;
            }

            $cityId = $company->city_id;

            // Cria ou atualiza a relação entre colaborador e cidade
            $created = DB::table('city_has_collaborator')->updateOrInsert(
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

            // Incrementa o contador, independente se foi update ou insert
            $relations_created++;

            // Informação opcional durante o loop (pode ser comentada se for muito verboso)
            $this->info("Relacionamento atualizado: Colaborador {$collaboratorId} → Cidade {$cityId}");
        }

        // Resultado final da operação
        $this->info("\nSincronização concluída.");
        $this->info("Relações criadas ou atualizadas: {$relations_created}");
        $this->info("Registros ignorados (sem cidade): {$relations_skipped}");
    }
}
