<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolPermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'inmuebles.ver',
            'inmuebles.crear',
            'inmuebles.editar',
            'inmuebles.eliminar',
            'censos.ver',
            'censos.crear',
            'censos.editar',
            'usuarios.gestionar',
            'roles.gestionar',
            'catalogos.gestionar',
            'territorio.gestionar',
            'reportes.ver',
            'reportes.exportar',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $roles = [
            'super_admin' => $permisos,
            'administrador' => [
                'inmuebles.ver', 'inmuebles.crear', 'inmuebles.editar', 'inmuebles.eliminar',
                'censos.ver', 'censos.crear', 'censos.editar',
                'usuarios.gestionar', 'catalogos.gestionar', 'territorio.gestionar',
                'reportes.ver', 'reportes.exportar',
            ],
            'coordinador' => [
                'inmuebles.ver', 'inmuebles.crear', 'inmuebles.editar',
                'censos.ver', 'censos.crear', 'censos.editar',
                'reportes.ver', 'reportes.exportar',
            ],
            'censista' => [
                'inmuebles.ver', 'inmuebles.crear', 'inmuebles.editar',
                'censos.ver', 'censos.crear', 'censos.editar',
            ],
            'consulta' => [
                'inmuebles.ver', 'censos.ver', 'reportes.ver',
            ],
        ];

        foreach ($roles as $nombre => $rolePermisos) {
            $role = Role::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermisos);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@censosviviendas.co'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin2026!'),
                'activo' => true,
            ]
        );

        $admin->assignRole('super_admin');
    }
}
