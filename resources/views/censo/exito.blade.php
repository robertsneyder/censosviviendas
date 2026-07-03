<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Censo guardado - Censo Viviendas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 text-gray-800 antialiased">
    <main class="mx-auto flex max-w-lg flex-col items-center px-4 py-16 text-center">
        <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="mb-2 text-2xl font-bold text-emerald-800">¡Censo guardado!</h1>
        <p class="mb-6 text-gray-600">El inmueble <strong>{{ $inmueble->direccion }}</strong> fue registrado correctamente.</p>
        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('censo.create') }}" class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-emerald-700">Nuevo censo</a>
            <a href="{{ url('/admin/inmuebles/'.$inmueble->id) }}" class="rounded-lg border border-emerald-300 px-6 py-2.5 text-sm font-medium text-emerald-700 hover:bg-emerald-50">Ver en panel</a>
        </div>
    </main>
</body>
</html>
