<div>
    {{-- Barra de progreso --}}
    <div class="mb-6">
        <div class="mb-2 flex justify-between text-sm text-gray-600">
            <span>Paso {{ $paso }} de {{ $totalPasos }}</span>
            <span>{{ round(($paso / $totalPasos) * 100) }}%</span>
        </div>
        <div class="h-2 overflow-hidden rounded-full bg-emerald-100">
            <div class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                 style="width: {{ ($paso / $totalPasos) * 100 }}%"></div>
        </div>
    </div>

    <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm sm:p-8">
        {{-- Paso 1: Territorio --}}
        @if ($paso === 1)
            <h2 class="mb-4 text-xl font-bold text-emerald-800">1. Identificación territorial</h2>
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Sector *</label>
                    <select wire:model="form.sector_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Seleccione un sector</option>
                        @foreach ($sectores as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                    @error('form.sector_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Dirección del inmueble *</label>
                    <input type="text" wire:model="form.direccion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ej: Calle Real #12">
                    @error('form.direccion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Referencia de ubicación</label>
                    <input type="text" wire:model="form.referencia_ubicacion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Frente a, esquina, color de la casa...">
                </div>
            </div>
        @endif

        {{-- Paso 2: Inmueble --}}
        @if ($paso === 2)
            <h2 class="mb-4 text-xl font-bold text-emerald-800">2. Datos generales del inmueble</h2>
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Tipo de inmueble *</label>
                    <select wire:model="form.tipo_inmueble" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Seleccione</option>
                        @foreach ($tiposInmueble as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                    @error('form.tipo_inmueble') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Estado de ocupación *</label>
                    <select wire:model="form.estado_ocupacion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Seleccione</option>
                        @foreach ($estadosOcupacion as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                    @error('form.estado_ocupacion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        {{-- Paso 3: Propietario --}}
        @if ($paso === 3)
            <h2 class="mb-4 text-xl font-bold text-emerald-800">3. Datos del propietario</h2>
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Nombre completo</label>
                    <input type="text" wire:model="form.propietario_nombre" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Documento</label>
                        <input type="text" wire:model="form.propietario_documento" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Teléfono</label>
                        <input type="tel" wire:model="form.propietario_telefono" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model.live="form.propietario_vive_aqui" id="vive_aqui" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="vive_aqui" class="text-sm font-medium">¿El propietario vive en este inmueble?</label>
                </div>
                @if (! $form['propietario_vive_aqui'])
                    <div>
                        <label class="mb-1 block text-sm font-medium">Lugar donde vive el propietario</label>
                        <input type="text" wire:model="form.propietario_lugar_residencia" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                @endif
            </div>
        @endif

        {{-- Paso 4: Encargado --}}
        @if ($paso === 4)
            <h2 class="mb-4 text-xl font-bold text-emerald-800">4. Persona encargada del inmueble</h2>
            @if ($form['propietario_vive_aqui'])
                <p class="rounded-lg bg-emerald-50 p-4 text-emerald-800">El propietario vive en el inmueble. Puede continuar al siguiente paso.</p>
            @else
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model.live="form.hay_encargado" id="hay_encargado" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="hay_encargado" class="text-sm font-medium">¿Hay una persona encargada del inmueble?</label>
                    </div>
                    @if ($form['hay_encargado'])
                        <div>
                            <label class="mb-1 block text-sm font-medium">Nombre completo del encargado</label>
                            <input type="text" wire:model="form.encargado_nombre" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium">Documento</label>
                                <input type="text" wire:model="form.encargado_documento" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium">Teléfono</label>
                                <input type="tel" wire:model="form.encargado_telefono" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Relación con el propietario</label>
                            <select wire:model="form.encargado_relacion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Seleccione</option>
                                @foreach ($relacionesEncargado as $valor => $etiqueta)
                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="form.encargado_vive_aqui" id="encargado_vive" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <label for="encargado_vive" class="text-sm font-medium">¿Vive en el inmueble?</label>
                        </div>
                    @endif
                </div>
            @endif
        @endif

        {{-- Paso 5: Unidades --}}
        @if ($paso === 5)
            <h2 class="mb-4 text-xl font-bold text-emerald-800">5. Unidades habitacionales</h2>
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium">¿Cuántas unidades habitacionales tiene el inmueble?</label>
                <input type="number" wire:model.blur="form.num_unidades" wire:change="inicializarUnidades" min="1" max="20" class="w-32 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            @foreach ($form['unidades'] as $index => $unidad)
                <div class="mb-6 rounded-xl border border-gray-200 p-4" wire:key="unidad-{{ $index }}">
                    <h3 class="mb-3 font-semibold text-gray-700">Unidad {{ $index + 1 }}</h3>
                    <div class="space-y-3">
                        <input type="text" wire:model="form.unidades.{{ $index }}.identificacion" placeholder="Identificación (Apto 101, habitación...)" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <select wire:model="form.unidades.{{ $index }}.tipo_unidad" class="rounded-lg border-gray-300 text-sm shadow-sm">
                                <option value="">Tipo de unidad</option>
                                @foreach ($tiposUnidad as $v => $e) <option value="{{ $v }}">{{ $e }}</option> @endforeach
                            </select>
                            <select wire:model.live="form.unidades.{{ $index }}.estado" class="rounded-lg border-gray-300 text-sm shadow-sm">
                                <option value="">Estado</option>
                                @foreach ($estadosUnidad as $v => $e) <option value="{{ $v }}">{{ $e }}</option> @endforeach
                            </select>
                        </div>
                        <input type="text" wire:model="form.unidades.{{ $index }}.ocupante_nombre" placeholder="Nombre de quien ocupa" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                        <select wire:model="form.unidades.{{ $index }}.calidad_ocupante" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                            <option value="">Calidad del ocupante</option>
                            @foreach ($calidadesOcupante as $v => $e) <option value="{{ $v }}">{{ $e }}</option> @endforeach
                        </select>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- Paso 6: Inquilinos --}}
        @if ($paso === 6)
            <h2 class="mb-4 text-xl font-bold text-emerald-800">6. Datos de inquilinos</h2>
            @php $tieneArrendadas = collect($form['unidades'])->contains('estado', 'arrendada'); @endphp
            @if (! $tieneArrendadas)
                <p class="rounded-lg bg-gray-50 p-4 text-gray-600">No hay unidades marcadas como arrendadas. Puede continuar.</p>
            @else
                @foreach ($form['unidades'] as $index => $unidad)
                    @if ($unidad['estado'] === 'arrendada')
                        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50/50 p-4" wire:key="inq-{{ $index }}">
                            <h3 class="mb-3 font-semibold text-amber-900">Inquilino — {{ $unidad['identificacion'] }}</h3>
                            <div class="space-y-3">
                                <input type="text" wire:model="form.unidades.{{ $index }}.inquilino_nombre" placeholder="Nombre del inquilino principal" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input type="text" wire:model="form.unidades.{{ $index }}.inquilino_documento" placeholder="Documento" class="rounded-lg border-gray-300 text-sm shadow-sm">
                                    <input type="tel" wire:model="form.unidades.{{ $index }}.inquilino_telefono" placeholder="Teléfono" class="rounded-lg border-gray-300 text-sm shadow-sm">
                                </div>
                                <input type="number" wire:model="form.unidades.{{ $index }}.inquilino_num_personas" placeholder="Nº personas en la unidad" min="1" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                <input type="text" wire:model="form.unidades.{{ $index }}.inquilino_arrendador_nombre" placeholder="Nombre del arrendador" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                <select wire:model="form.unidades.{{ $index }}.inquilino_relacion_arrendador" class="w-full rounded-lg border-gray-300 text-sm shadow-sm">
                                    <option value="">Relación del arrendador</option>
                                    @foreach ($relacionesArrendador as $v => $e) <option value="{{ $v }}">{{ $e }}</option> @endforeach
                                </select>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input type="number" wire:model="form.unidades.{{ $index }}.inquilino_valor_arriendo" placeholder="Valor arriendo (opcional)" step="0.01" class="rounded-lg border-gray-300 text-sm shadow-sm">
                                    <input type="text" wire:model="form.unidades.{{ $index }}.inquilino_tiempo_viviendo" placeholder="Tiempo viviendo (opcional)" class="rounded-lg border-gray-300 text-sm shadow-sm">
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        @endif

        {{-- Paso 7: Observaciones --}}
        @if ($paso === 7)
            <h2 class="mb-4 text-xl font-bold text-emerald-800">7. Observaciones del censista</h2>
            <textarea wire:model="form.observaciones" rows="5" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ej: El propietario vive fuera del barrio, no se encontró nadie..."></textarea>
        @endif

        {{-- Paso 8: Control --}}
        @if ($paso === 8)
            <h2 class="mb-4 text-xl font-bold text-emerald-800">8. Control del censo</h2>
            <div class="space-y-4">
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm text-gray-600">Censista</p>
                    <p class="font-medium">{{ auth()->user()->name }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm text-gray-600">Fecha y hora</p>
                    <p class="font-medium">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">¿La información quedó completa? *</label>
                    <select wire:model="form.estado_completitud" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach ($estadosCompletitud as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="form.requiere_nueva_visita" id="nueva_visita" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="nueva_visita" class="text-sm font-medium">Requiere nueva visita</label>
                </div>
            </div>
        @endif

        {{-- Navegación --}}
        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            @if ($paso > 1)
                <button type="button" wire:click="anterior" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Anterior
                </button>
            @else
                <div></div>
            @endif

            @if ($paso < $totalPasos)
                <button type="button" wire:click="siguiente" class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-emerald-700">
                    Siguiente
                </button>
            @else
                <button type="button" wire:click="guardar" wire:loading.attr="disabled" class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="guardar">Guardar censo</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>
            @endif
        </div>
    </div>
</div>
