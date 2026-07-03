<?php

use App\Livewire\CensoWizard;
use App\Models\Inmueble;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/censo/nuevo', CensoWizard::class)
        ->name('censo.create')
        ->middleware('can:censos.crear');

    Route::get('/censo/{inmueble}/editar', CensoWizard::class)
        ->name('censo.edit')
        ->middleware('can:censos.editar');

    Route::get('/censo/{inmueble}/exito', function (Inmueble $inmueble) {
        return view('censo.exito', compact('inmueble'));
    })->name('censo.exito');
});
