<?php

namespace Database\Seeders;

use App\Models\Collaborator;
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
       
        Section::create(['name'=> 'Flv - Hortifruti',]);
        Section::create(['name'=> 'Flc - Frios',]);
        Section::create(['name'=> 'Padaria',]);
        Section::create(['name'=> 'Mercearia',]);
        Section::create(['name'=> 'Frente de Caixa',]);
        Section::create(['name'=> 'Depósito',]);
       
        Section::create(['name'=> 'Floricultura',]);
        
        Section::create(['name'=> 'Separação',]);
        Section::create(['name'=> 'Conferência',]);
        // User::factory(count: 1)->create();

        $this->call([
            PermissionsSeeder::class,
        ]);
    }
}
