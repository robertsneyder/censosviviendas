<?php

namespace App\Filament\Widgets;

use App\Models\Inmueble;
use App\Services\TerritorioService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CensoStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $query = app(TerritorioService::class)->filtrarInmueblesPorUsuario(Inmueble::query(), $user);

        $total = (clone $query)->count();
        $completos = (clone $query)->where('estado_completitud', 'completo')->count();
        $pendientes = (clone $query)->where('requiere_nueva_visita', true)->count();

        return [
            Stat::make('Inmuebles censados', $total)
                ->description('Total registrados')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),
            Stat::make('Censos completos', $completos)
                ->description($total > 0 ? round(($completos / $total) * 100).'% del total' : 'Sin registros')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Requieren visita', $pendientes)
                ->description('Pendientes de nueva visita')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),
        ];
    }
}
