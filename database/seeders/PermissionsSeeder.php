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
        Permission::findOrCreate('Lista de usuários');
        Permission::findOrCreate('Formulário de criação dos usuários');
        Permission::findOrCreate('Salvar usuários');
        Permission::findOrCreate('Formulário de edição dos usuários');
        Permission::findOrCreate('Atualizar usuários');
        Permission::findOrCreate('Deletar usuários');

        Permission::findOrCreate('Lista de colaboradores');
        Permission::findOrCreate('Formulário de criação dos colaboradores');
        Permission::findOrCreate('Salvar colaboradores');
        Permission::findOrCreate('Formulário de edição dos colaboradores');
        Permission::findOrCreate('Atualizar colaboradores');
        Permission::findOrCreate('Deletar colaboradores');

        $user = User::where('id', '=', env('DEV_USER_ID'))->first();
        $user->givePermissionTo(Permission::all());
    }
}
