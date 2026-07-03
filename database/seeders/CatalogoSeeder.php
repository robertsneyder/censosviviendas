<?php

namespace Database\Seeders;

use App\Models\CatalogoGrupo;
use App\Models\CatalogoOpcion;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $catalogos = [
            'tipo_inmueble' => [
                'nombre' => 'Tipo de inmueble',
                'opciones' => [
                    'casa_familiar' => 'Casa familiar',
                    'casa_habitaciones_arrendadas' => 'Casa con habitaciones arrendadas',
                    'edificio_apartamentos' => 'Edificio de apartamentos',
                    'local_comercial' => 'Local comercial',
                    'inmueble_mixto' => 'Inmueble mixto: vivienda y comercio',
                    'lote_desocupado' => 'Lote / inmueble desocupado',
                    'otro' => 'Otro',
                ],
            ],
            'estado_ocupacion' => [
                'nombre' => 'Estado de ocupación',
                'opciones' => [
                    'habita_propietario' => 'Habita el propietario',
                    'habita_familiar' => 'Habita un familiar del propietario',
                    'habita_encargado' => 'Habita un encargado',
                    'totalmente_arrendado' => 'Está totalmente arrendado',
                    'parcialmente_arrendado' => 'Está parcialmente arrendado',
                    'desocupado' => 'Está desocupado',
                ],
            ],
            'tipo_unidad' => [
                'nombre' => 'Tipo de unidad',
                'opciones' => [
                    'apartamento' => 'Apartamento',
                    'habitacion' => 'Habitación',
                    'casa' => 'Casa',
                    'local' => 'Local',
                    'otro' => 'Otro',
                ],
            ],
            'estado_unidad' => [
                'nombre' => 'Estado de unidad',
                'opciones' => [
                    'ocupada_propietario' => 'Ocupada por propietario',
                    'ocupada_familiar' => 'Ocupada por familiar',
                    'arrendada' => 'Arrendada',
                    'desocupada' => 'Desocupada',
                ],
            ],
            'calidad_ocupante' => [
                'nombre' => 'Calidad del ocupante',
                'opciones' => [
                    'propietario' => 'Propietario',
                    'familiar' => 'Familiar',
                    'inquilino' => 'Inquilino',
                    'encargado' => 'Encargado',
                    'otro' => 'Otro',
                ],
            ],
            'relacion_encargado' => [
                'nombre' => 'Relación del encargado',
                'opciones' => [
                    'familiar' => 'Familiar',
                    'administrador' => 'Administrador',
                    'arrendatario_principal' => 'Arrendatario principal',
                    'cuidador' => 'Cuidador',
                    'otro' => 'Otro',
                ],
            ],
            'relacion_arrendador' => [
                'nombre' => 'Relación del arrendador',
                'opciones' => [
                    'propietario' => 'Propietario',
                    'encargado' => 'Encargado',
                    'administrador' => 'Administrador',
                    'familiar_propietario' => 'Familiar del propietario',
                    'otro' => 'Otro',
                ],
            ],
            'estado_completitud' => [
                'nombre' => 'Estado del censo',
                'opciones' => [
                    'completo' => 'Completo',
                    'parcial' => 'Parcial',
                    'incompleto' => 'Incompleto',
                ],
            ],
        ];

        foreach ($catalogos as $slug => $data) {
            $grupo = CatalogoGrupo::updateOrCreate(
                ['slug' => $slug],
                ['nombre' => $data['nombre'], 'activo' => true]
            );

            $orden = 1;
            foreach ($data['opciones'] as $valor => $etiqueta) {
                CatalogoOpcion::updateOrCreate(
                    ['catalogo_grupo_id' => $grupo->id, 'valor' => $valor],
                    ['etiqueta' => $etiqueta, 'orden' => $orden++, 'activo' => true]
                );
            }
        }
    }
}
