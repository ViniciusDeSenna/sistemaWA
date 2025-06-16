<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Collaborator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CollaboratorCityRelationMaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:collaborator-city-relation-maker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes Colaborator "City" String value, into a "Collaborator_has_city" Relation, based into the String of both tables, City and Collaborator..';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando a migração relacionando colaboradores e cidades...");

        $collaborators = \App\Models\Collaborator::all();
        $cities = \App\Models\City::all()->pluck('id', 'name'); // [name => id]

        $naoEncontrados = [];
        $relacoesCriadas = 0;

        foreach ($collaborators as $collaborator) {
            $colabCityName = ucfirst(strtolower(trim($collaborator->city)));

            if (empty($colabCityName)) {
                continue;
            }

            $cityId = $cities[$colabCityName] ?? null;

            if ($cityId) {
                // Verifica se a relação já existe
                $existe = DB::table('city_has_collaborator')
                    ->where('collaborator_id', $collaborator->id)
                    ->where('city_id', $cityId)
                    ->exists();

                if (!$existe) {
                    DB::table('city_has_collaborator')->insert([
                        'collaborator_id' => $collaborator->id,
                        'city_id' => $cityId,
                        'active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $relacoesCriadas++;
                }
            } else {
                $naoEncontrados[] = [
                    'colaborador' => $collaborator->name,
                    'cidade_nao_encontrada' => $collaborator->city
                ];
            }
        }

        // Resultado final
        $this->info("\nMigração finalizada!");
        $this->info("Total de relações criadas: $relacoesCriadas");

        if (count($naoEncontrados)) {
            $this->warn("\nColaboradores com cidades não encontradas:");
            foreach ($naoEncontrados as $item) {
                $this->line("- {$item['colaborador']} → \"{$item['cidade_nao_encontrada']}\"");
            }
        } else {
            $this->info("Todas as cidades foram reconhecidas.");
        }
    }
}
