<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::findOrCreate('index users');
        Permission::findOrCreate('store users');
        Permission::findOrCreate('edit users');

        $user = User::where('id', '=', env('DEV_USER_ID'))->first();
        $user->givePermissionTo(Permission::all());
    }
}
