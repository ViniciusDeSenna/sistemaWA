<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class MigrateCitiesFromCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-cities-from-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra o campo city na tabela Companies, para a tabela City, fazendo a ligação na tabela Company_has_City';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando migração de cidades...");

        $companies = Company::all();
        $total = $companies->count();
        
        $this->info("Processando $total estabelecimentos...");

        $cadastradas = 0;

        foreach($companies as $company){
            $cityName = trim($company->city);
            
            if (empty($cityName)){
                 $this->warn("Empresa ID {$company->id} sem cidade definida. Ignorando.");
                continue;
            }

            $city = City::firstOrCreate(['name'=> $cityName,
                                        'is_active'=>true,]);

            if ($city->wasRecentlyCreated){
                $this->info("Cidade '{$cityName}' adicionada!");
                $cadastradas++;
            }
            DB::table('company_has_city')->updateOrInsert(
                [
                    'company_id' => $company->id,
                    'city_id'    => $city->id
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            $this->info("Relação Estabelecimento-Cidade criada (company_id: {$company->id}, city_id: {$city->id})");
        }
        $this->info("Migração finalizada. Total de cidades cadastradas: $cadastradas");
    }
}
