<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Censo Viviendas - Junta Niño Jesús</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 text-gray-800 antialiased">
    <main class="mx-auto flex max-w-4xl flex-col items-center px-4 py-16 text-center">
        <div class="mb-4 inline-flex items-center rounded-full bg-emerald-100 px-4 py-1 text-sm font-medium text-emerald-800">
            Junta de Acción Comunal — Barrio Niño Jesús
        </div>
        <h1 class="mb-4 text-4xl font-bold tracking-tight text-emerald-900 sm:text-5xl">
            Censo de Viviendas
        </h1>
        <p class="mb-10 max-w-2xl text-lg text-gray-600">
            Plataforma web para registrar y gestionar el censo de inmuebles del barrio,
            organizada por departamentos, municipios, comunas, barrios y sectores.
        </p>
        <div class="flex flex-col gap-4 sm:flex-row">
            <a href="{{ url('/admin') }}"
               class="rounded-xl bg-emerald-600 px-8 py-3 font-semibold text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700">
                Acceder al panel
            </a>
            @auth
                @can('censos.crear')
                    <a href="{{ route('censo.create') }}"
                       class="rounded-xl border-2 border-emerald-600 px-8 py-3 font-semibold text-emerald-700 transition hover:bg-emerald-50">
                        Nuevo censo
                    </a>
                @endcan
            @endauth
        </div>
        <div class="mt-16 grid w-full gap-6 text-left sm:grid-cols-3">
            <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="mb-2 font-semibold text-emerald-800">Territorial</h3>
                <p class="text-sm text-gray-600">División por departamento, municipio, comuna, barrio y sector.</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="mb-2 font-semibold text-emerald-800">Formulario guiado</h3>
                <p class="text-sm text-gray-600">Censo paso a paso, optimizado para celular y tablet.</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="mb-2 font-semibold text-emerald-800">Control de acceso</h3>
                <p class="text-sm text-gray-600">Usuarios con roles y permisos según su alcance territorial.</p>
            </div>
        </div>
    </main>
</body>
</html>
