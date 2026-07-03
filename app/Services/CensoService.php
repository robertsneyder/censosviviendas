<?php

namespace App\Services;

use App\Models\Encargado;
use App\Models\Inmueble;
use App\Models\Inquilino;
use App\Models\Propietario;
use App\Models\UnidadHabitacional;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CensoService
{
    public function guardar(array $data, ?Inmueble $inmueble = null, ?User $censista = null): Inmueble
    {
        return DB::transaction(function () use ($data, $inmueble, $censista) {
            $inmueble = $inmueble ?? new Inmueble();

            $inmueble->fill([
                'sector_id' => $data['sector_id'],
                'direccion' => $data['direccion'],
                'referencia_ubicacion' => $data['referencia_ubicacion'] ?? null,
                'tipo_inmueble' => $data['tipo_inmueble'],
                'estado_ocupacion' => $data['estado_ocupacion'],
                'observaciones' => $data['observaciones'] ?? null,
                'censista_id' => $censista?->id ?? $data['censista_id'] ?? null,
                'fecha_registro' => now(),
                'estado_completitud' => $data['estado_completitud'] ?? 'parcial',
                'requiere_nueva_visita' => $data['requiere_nueva_visita'] ?? false,
            ]);
            $inmueble->save();

            Propietario::updateOrCreate(
                ['inmueble_id' => $inmueble->id],
                [
                    'nombre_completo' => $data['propietario_nombre'] ?? null,
                    'documento' => $data['propietario_documento'] ?? null,
                    'telefono' => $data['propietario_telefono'] ?? null,
                    'vive_en_inmueble' => $data['propietario_vive_aqui'] ?? false,
                    'lugar_residencia' => $data['propietario_lugar_residencia'] ?? null,
                ]
            );

            $hayEncargado = ! ($data['propietario_vive_aqui'] ?? false) && ($data['hay_encargado'] ?? false);

            Encargado::updateOrCreate(
                ['inmueble_id' => $inmueble->id],
                [
                    'hay_encargado' => $hayEncargado,
                    'nombre_completo' => $hayEncargado ? ($data['encargado_nombre'] ?? null) : null,
                    'documento' => $hayEncargado ? ($data['encargado_documento'] ?? null) : null,
                    'telefono' => $hayEncargado ? ($data['encargado_telefono'] ?? null) : null,
                    'relacion_propietario' => $hayEncargado ? ($data['encargado_relacion'] ?? null) : null,
                    'vive_en_inmueble' => $hayEncargado ? ($data['encargado_vive_aqui'] ?? false) : false,
                ]
            );

            $inmueble->unidades()->each(function (UnidadHabitacional $unidad) {
                $unidad->inquilino?->delete();
                $unidad->delete();
            });

            foreach ($data['unidades'] ?? [] as $unidadData) {
                $unidad = $inmueble->unidades()->create([
                    'identificacion' => $unidadData['identificacion'],
                    'tipo_unidad' => $unidadData['tipo_unidad'],
                    'estado' => $unidadData['estado'],
                    'ocupante_nombre' => $unidadData['ocupante_nombre'] ?? null,
                    'ocupante_documento' => $unidadData['ocupante_documento'] ?? null,
                    'ocupante_telefono' => $unidadData['ocupante_telefono'] ?? null,
                    'calidad_ocupante' => $unidadData['calidad_ocupante'] ?? null,
                    'arrendador_nombre' => $unidadData['arrendador_nombre'] ?? null,
                    'arrendador_telefono' => $unidadData['arrendador_telefono'] ?? null,
                ]);

                if (($unidadData['estado'] ?? '') === 'arrendada' && ! empty($unidadData['inquilino_nombre'])) {
                    Inquilino::create([
                        'unidad_habitacional_id' => $unidad->id,
                        'nombre_completo' => $unidadData['inquilino_nombre'] ?? null,
                        'documento' => $unidadData['inquilino_documento'] ?? null,
                        'telefono' => $unidadData['inquilino_telefono'] ?? null,
                        'num_personas' => $unidadData['inquilino_num_personas'] ?? null,
                        'arrendador_nombre' => $unidadData['inquilino_arrendador_nombre'] ?? null,
                        'relacion_arrendador' => $unidadData['inquilino_relacion_arrendador'] ?? null,
                        'valor_arriendo' => $unidadData['inquilino_valor_arriendo'] ?? null,
                        'tiempo_viviendo' => $unidadData['inquilino_tiempo_viviendo'] ?? null,
                    ]);
                }
            }

            return $inmueble->fresh(['propietario', 'encargado', 'unidades.inquilino', 'sector']);
        });
    }
}
