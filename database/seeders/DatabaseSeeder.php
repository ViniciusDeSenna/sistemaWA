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
        
        Section::create(['name'=> 'Flv',]);
        Section::create(['name'=> 'Flc',]);
        Section::create(['name'=> 'Floricultura',]);
        Section::create(['name'=> 'Mercearia',]);
        
        Section::create(['name'=> 'Frente de Caixa',]);
        // User::factory(count: 1)->create();

        $this->call([
            PermissionsSeeder::class,
        ]);
    }
}
