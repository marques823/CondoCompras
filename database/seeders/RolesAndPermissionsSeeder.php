<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Criar Roles
        $admin = Role::updateOrCreate(['name' => 'admin'], ['label' => 'Super Admin']);
        $administradora = Role::updateOrCreate(['name' => 'administradora'], ['label' => 'Empresa Administradora']);
        $gerente = Role::updateOrCreate(['name' => 'gerente'], ['label' => 'Gerente Operacional']);
        $zelador = Role::updateOrCreate(['name' => 'zelador'], ['label' => 'Zelador do Condomínio']);

        // 2. Criar Permissões (Exemplos)
        $permissions = [
            'manage_administradoras',
            'manage_gerentes',
            'manage_condominios',
            'manage_zeladores',
            'manage_demandas',
            'view_reports',
            'create_demandas',
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['name' => $perm], ['label' => ucwords(str_replace('_', ' ', $perm))]);
        }

        // 3. Atribuir permissões às roles (Simplificado para o momento)
        
        // Admin tem tudo (lógica costuma ser no Gate::before, mas vamos colocar aqui também)
        $admin->permissions()->sync(Permission::all());

        // Administradora gerencia seus gerentes e vê tudo da empresa
        $administradora->permissions()->sync(Permission::whereIn('name', [
            'manage_gerentes',
            'manage_condominios',
            'manage_demandas',
            'view_reports'
        ])->get());

        // Gerente operacional
        $gerente->permissions()->sync(Permission::whereIn('name', [
            'manage_condominios',
            'manage_zeladores',
            'manage_demandas',
            'create_demandas'
        ])->get());

        // Zelador focado em demandas
        $zelador->permissions()->sync(Permission::whereIn('name', [
            'create_demandas',
            'manage_demandas'
        ])->get());
    }
}
