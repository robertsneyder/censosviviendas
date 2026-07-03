<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Censo Viviendas - Junta Niño Jesús</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 text-gray-800 antialiased">
    <header class="border-b border-emerald-100 bg-white/80 backdrop-blur sticky top-0 z-50">
        <div class="mx-auto flex max-w-3xl items-center justify-between px-4 py-3">
            <a href="{{ url('/') }}" class="font-semibold text-emerald-700">Censo Viviendas</a>
            <a href="{{ url('/admin') }}" class="text-sm text-emerald-600 hover:underline">Panel admin</a>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
