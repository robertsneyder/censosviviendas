<?php

namespace App\Livewire;

use App\Models\CatalogoGrupo;
use App\Models\Inmueble;
use App\Models\Sector;
use App\Services\CensoService;
use App\Services\TerritorioService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.censo')]
class CensoWizard extends Component
{
    public int $paso = 1;

    public int $totalPasos = 8;

    public ?int $inmuebleId = null;

    public array $form = [
        'sector_id' => '',
        'direccion' => '',
        'referencia_ubicacion' => '',
        'tipo_inmueble' => '',
        'estado_ocupacion' => '',
        'propietario_nombre' => '',
        'propietario_documento' => '',
        'propietario_telefono' => '',
        'propietario_vive_aqui' => false,
        'propietario_lugar_residencia' => '',
        'hay_encargado' => false,
        'encargado_nombre' => '',
        'encargado_documento' => '',
        'encargado_telefono' => '',
        'encargado_relacion' => '',
        'encargado_vive_aqui' => false,
        'num_unidades' => 1,
        'unidades' => [],
        'observaciones' => '',
        'estado_completitud' => 'parcial',
        'requiere_nueva_visita' => false,
    ];

    public function mount(?Inmueble $inmueble = null): void
    {
        if ($inmueble) {
            abort_unless(
                app(TerritorioService::class)->usuarioPuedeAccederInmueble(auth()->user(), $inmueble),
                403
            );

            $this->inmuebleId = $inmueble->id;
            $inmueble->load(['propietario', 'encargado', 'unidades.inquilino']);

            $this->form = [
                'sector_id' => $inmueble->sector_id,
                'direccion' => $inmueble->direccion,
                'referencia_ubicacion' => $inmueble->referencia_ubicacion ?? '',
                'tipo_inmueble' => $inmueble->tipo_inmueble,
                'estado_ocupacion' => $inmueble->estado_ocupacion,
                'propietario_nombre' => $inmueble->propietario?->nombre_completo ?? '',
                'propietario_documento' => $inmueble->propietario?->documento ?? '',
                'propietario_telefono' => $inmueble->propietario?->telefono ?? '',
                'propietario_vive_aqui' => $inmueble->propietario?->vive_en_inmueble ?? false,
                'propietario_lugar_residencia' => $inmueble->propietario?->lugar_residencia ?? '',
                'hay_encargado' => $inmueble->encargado?->hay_encargado ?? false,
                'encargado_nombre' => $inmueble->encargado?->nombre_completo ?? '',
                'encargado_documento' => $inmueble->encargado?->documento ?? '',
                'encargado_telefono' => $inmueble->encargado?->telefono ?? '',
                'encargado_relacion' => $inmueble->encargado?->relacion_propietario ?? '',
                'encargado_vive_aqui' => $inmueble->encargado?->vive_en_inmueble ?? false,
                'num_unidades' => max(1, $inmueble->unidades->count()),
                'unidades' => $inmueble->unidades->map(fn ($u) => [
                    'identificacion' => $u->identificacion,
                    'tipo_unidad' => $u->tipo_unidad,
                    'estado' => $u->estado,
                    'ocupante_nombre' => $u->ocupante_nombre ?? '',
                    'ocupante_documento' => $u->ocupante_documento ?? '',
                    'ocupante_telefono' => $u->ocupante_telefono ?? '',
                    'calidad_ocupante' => $u->calidad_ocupante ?? '',
                    'arrendador_nombre' => $u->arrendador_nombre ?? '',
                    'arrendador_telefono' => $u->arrendador_telefono ?? '',
                    'inquilino_nombre' => $u->inquilino?->nombre_completo ?? '',
                    'inquilino_documento' => $u->inquilino?->documento ?? '',
                    'inquilino_telefono' => $u->inquilino?->telefono ?? '',
                    'inquilino_num_personas' => $u->inquilino?->num_personas ?? '',
                    'inquilino_arrendador_nombre' => $u->inquilino?->arrendador_nombre ?? '',
                    'inquilino_relacion_arrendador' => $u->inquilino?->relacion_arrendador ?? '',
                    'inquilino_valor_arriendo' => $u->inquilino?->valor_arriendo ?? '',
                    'inquilino_tiempo_viviendo' => $u->inquilino?->tiempo_viviendo ?? '',
                ])->toArray(),
                'observaciones' => $inmueble->observaciones ?? '',
                'estado_completitud' => $inmueble->estado_completitud,
                'requiere_nueva_visita' => $inmueble->requiere_nueva_visita,
            ];
        } else {
            $this->inicializarUnidades();
            if ($sectorId = auth()->user()?->sector_id) {
                $this->form['sector_id'] = $sectorId;
            }
        }
    }

    public function inicializarUnidades(): void
    {
        $num = max(1, (int) $this->form['num_unidades']);
        $this->form['num_unidades'] = $num;
        $existentes = $this->form['unidades'];
        $this->form['unidades'] = [];

        for ($i = 0; $i < $num; $i++) {
            $this->form['unidades'][$i] = $existentes[$i] ?? $this->unidadVacia($i + 1);
        }
    }

    protected function unidadVacia(int $numero): array
    {
        return [
            'identificacion' => "Unidad {$numero}",
            'tipo_unidad' => '',
            'estado' => '',
            'ocupante_nombre' => '',
            'ocupante_documento' => '',
            'ocupante_telefono' => '',
            'calidad_ocupante' => '',
            'arrendador_nombre' => '',
            'arrendador_telefono' => '',
            'inquilino_nombre' => '',
            'inquilino_documento' => '',
            'inquilino_telefono' => '',
            'inquilino_num_personas' => '',
            'inquilino_arrendador_nombre' => '',
            'inquilino_relacion_arrendador' => '',
            'inquilino_valor_arriendo' => '',
            'inquilino_tiempo_viviendo' => '',
        ];
    }

    public function siguiente(): void
    {
        $this->validate($this->reglasPaso());
        if ($this->paso < $this->totalPasos) {
            $this->paso++;
        }
    }

    public function anterior(): void
    {
        if ($this->paso > 1) {
            $this->paso--;
        }
    }

    public function guardar(): void
    {
        $this->validate($this->reglasPaso());

        $inmueble = $this->inmuebleId
            ? Inmueble::findOrFail($this->inmuebleId)
            : null;

        $registro = app(CensoService::class)->guardar($this->form, $inmueble, auth()->user());

        session()->flash('success', 'Censo guardado correctamente.');

        $this->redirect(route('censo.exito', $registro));
    }

    protected function reglasPaso(): array
    {
        return match ($this->paso) {
            1 => [
                'form.sector_id' => 'required|exists:sectores,id',
                'form.direccion' => 'required|string|max:255',
            ],
            2 => [
                'form.tipo_inmueble' => 'required|string',
                'form.estado_ocupacion' => 'required|string',
            ],
            3 => [],
            4 => $this->form['propietario_vive_aqui'] ? [] : [
                'form.hay_encargado' => 'boolean',
            ],
            5 => [
                'form.num_unidades' => 'required|integer|min:1|max:20',
            ],
            8 => [
                'form.estado_completitud' => 'required|string',
            ],
            default => [],
        };
    }

    public function getSectoresProperty(): array
    {
        $query = Sector::with('barrio.comuna.municipio.departamento')->where('activo', true);

        if ($user = auth()->user()) {
            if ($user->sector_id) {
                $query->where('id', $user->sector_id);
            } elseif ($user->barrio_id) {
                $query->where('barrio_id', $user->barrio_id);
            }
        }

        return $query->get()->mapWithKeys(fn (Sector $s) => [$s->id => $s->rutaTerritorial()])->toArray();
    }

    public function render()
    {
        return view('livewire.censo-wizard', [
            'sectores' => $this->sectores,
            'tiposInmueble' => CatalogoGrupo::opcionesPorSlug('tipo_inmueble'),
            'estadosOcupacion' => CatalogoGrupo::opcionesPorSlug('estado_ocupacion'),
            'tiposUnidad' => CatalogoGrupo::opcionesPorSlug('tipo_unidad'),
            'estadosUnidad' => CatalogoGrupo::opcionesPorSlug('estado_unidad'),
            'calidadesOcupante' => CatalogoGrupo::opcionesPorSlug('calidad_ocupante'),
            'relacionesEncargado' => CatalogoGrupo::opcionesPorSlug('relacion_encargado'),
            'relacionesArrendador' => CatalogoGrupo::opcionesPorSlug('relacion_arrendador'),
            'estadosCompletitud' => CatalogoGrupo::opcionesPorSlug('estado_completitud'),
        ]);
    }
}
