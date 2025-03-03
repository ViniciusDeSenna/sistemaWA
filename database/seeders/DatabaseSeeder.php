<?php

namespace Database\Seeders;

use App\Models\Collaborator;
use App\Models\Establishment;
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
        
        // User::factory(count: 1)->create();
        
        $this->call([
            PermissionsSeeder::class,
        ]);
    }
}
