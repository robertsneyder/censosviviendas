<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 10)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->string('codigo', 10)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('comunas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipio_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('barrios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comuna_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('sectores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barrio_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sectores');
        Schema::dropIfExists('barrios');
        Schema::dropIfExists('comunas');
        Schema::dropIfExists('municipios');
        Schema::dropIfExists('departamentos');
    }
};
