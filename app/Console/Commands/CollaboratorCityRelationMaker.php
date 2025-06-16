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
        $cities = \App\Models\City::all();

        $relacoesCriadas = 0;
        $naoEncontrados = [];

        // Mapeamento manual para correções conhecidas, com nomes já normalizados
        $correcoes = [
            'camboriu' => 'balneario camboriu',
            'balnearios camboriu' => 'balneario camboriu',
            'balneario camburiu' => 'balneario camboriu',
            'cidade itajai' => 'itajai',
            'cidade brusque' => 'brusque',
            'gabiruba' => 'guabiruba',
            'busque' => 'brusque',
        ];

        foreach ($collaborators as $collaborator) {
            $colabCityOriginal = $collaborator->city ?? '';


            $normalized = $this->normalizeCityName($colabCityOriginal);

            // Aplica correção manual se existir
            if (isset($correcoes[$normalized])) {
                $normalized = $correcoes[$normalized];
            }

            // Busca a cidade normalizada na lista
            $city = $cities->first(function ($c) use ($normalized) {
                return $this->normalizeCityName($c->name) === $normalized;
            });

            if ($city) {
                // Verifica se a relação já existe
                $existe = DB::table('city_has_collaborator')
                    ->where('collaborator_id', $collaborator->id)
                    ->where('city_id', $city->id)
                    ->exists();

                if (!$existe) {
                    DB::table('city_has_collaborator')->insert([
                        'collaborator_id' => $collaborator->id,
                        'city_id' => $city->id,
                        'active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $relacoesCriadas++;
                }
            } else {
                $naoEncontrados[] = [
                    'colaborador' => $collaborator->name,
                    'cidade_nao_encontrada' => $colabCityOriginal
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

    /**
     * Função auxiliar para normalizar nomes de cidade.
     */
    private function normalizeCityName(string $name): string
    {
        $name = mb_strtolower($name, 'UTF-8');

        $search  = ['á','à','ã','â','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','õ','ô','ö','ú','ù','û','ü','ç'];
        $replace = ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c'];
        $name = str_replace($search, $replace, $name);

        // Remove tudo que não for letra minúscula ou espaço
        $name = preg_replace('/[^a-z\s]/u', '', $name);

        // Remove espaços extras no início/fim e espaços duplos dentro da string
        $name = trim(preg_replace('/\s+/', ' ', $name));

        return $name;
    }

}
