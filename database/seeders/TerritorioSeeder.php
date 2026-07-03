<?php

namespace Database\Seeders;

use App\Models\Barrio;
use App\Models\Comuna;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Sector;
use Illuminate\Database\Seeder;

class TerritorioSeeder extends Seeder
{
    public function run(): void
    {
        $departamento = Departamento::updateOrCreate(
            ['codigo' => '08'],
            ['nombre' => 'Atlántico', 'activo' => true]
        );

        $municipio = Municipio::updateOrCreate(
            ['departamento_id' => $departamento->id, 'codigo' => '08001'],
            ['nombre' => 'Barranquilla', 'activo' => true]
        );

        $comuna = Comuna::updateOrCreate(
            ['municipio_id' => $municipio->id, 'nombre' => 'Comuna Metropolitana'],
            ['activo' => true]
        );

        $barrio = Barrio::updateOrCreate(
            ['comuna_id' => $comuna->id, 'nombre' => 'Barrio Niño Jesús'],
            ['activo' => true]
        );

        $sectores = [
            'Calle Real',
            'Los Balcones',
            'Calle de La Bombonera',
            'Los Almendros',
            'La Draga',
        ];

        foreach ($sectores as $nombre) {
            Sector::updateOrCreate(
                ['barrio_id' => $barrio->id, 'nombre' => $nombre],
                ['activo' => true]
            );
        }
    }
}
