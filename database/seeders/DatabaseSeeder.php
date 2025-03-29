<?php

namespace Database\Seeders;

use App\Models\Collaborator;
use App\Models\ConfigTable;
use App\Models\Establishment;
use App\Models\Section;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        if (!User::findByEmail('dev@dev.com')->exists()) {
            User::factory()->create([
                'name' => 'Developer',
                'email' => 'dev@dev.com',
            ]);
        }
        
        //Section::create(['name'=> 'Flv - Hortifruti',]);
        //Section::create(['name'=> 'Flc - Frios',]);
        //Section::create(['name'=> 'Padaria',]);
        //Section::create(['name'=> 'Mercearia',]);
        //Section::create(['name'=> 'Frente de Caixa',]);
        //Section::create(['name'=> 'Depósito',]);
       
        
        //Section::create(['name'=> 'Açougue Abastecimento',]);
        //Section::create(['name'=> 'Açougue Cortes/Manipulação',]);
        //Section::create(['name'=> 'FLV - Central - Bistek',]);
        Section::create(['name'=> 'Diária Proporcional',]);
        //Section::create(['name'=> 'Conferência',]);

        //Section::create(['name'=> 'Floricultura',]);
        
        //Section::create(['name'=> 'Separação',]);
        //Section::create(['name'=> 'Conferência',]);

        // Criando um novo registro na tabela config_table
        //ConfigTable::create([
        //    'id' => 'inss_default', // Se o id for um UUID, senão pode ser um valor fixo
        //    'value' => 7.5,
        //]);
        
        //ConfigTable::create([
        //    'id' => 'tax_default',
        //    'value' => 14.32,
        //]);

    }
}
