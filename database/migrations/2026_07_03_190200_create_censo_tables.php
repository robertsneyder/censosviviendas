<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inmuebles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->constrained('sectores');
            $table->string('direccion');
            $table->string('referencia_ubicacion')->nullable();
            $table->string('tipo_inmueble');
            $table->string('estado_ocupacion');
            $table->text('observaciones')->nullable();
            $table->foreignId('censista_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_registro')->nullable();
            $table->string('estado_completitud')->default('parcial');
            $table->boolean('requiere_nueva_visita')->default(false);
            $table->timestamps();
        });

        Schema::create('propietarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmueble_id')->constrained()->cascadeOnDelete();
            $table->string('nombre_completo')->nullable();
            $table->string('documento')->nullable();
            $table->string('telefono')->nullable();
            $table->boolean('vive_en_inmueble')->default(false);
            $table->string('lugar_residencia')->nullable();
            $table->timestamps();
        });

        Schema::create('encargados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmueble_id')->constrained()->cascadeOnDelete();
            $table->boolean('hay_encargado')->default(false);
            $table->string('nombre_completo')->nullable();
            $table->string('documento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('relacion_propietario')->nullable();
            $table->boolean('vive_en_inmueble')->default(false);
            $table->timestamps();
        });

        Schema::create('unidades_habitacionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inmueble_id')->constrained()->cascadeOnDelete();
            $table->string('identificacion');
            $table->string('tipo_unidad');
            $table->string('estado');
            $table->string('ocupante_nombre')->nullable();
            $table->string('ocupante_documento')->nullable();
            $table->string('ocupante_telefono')->nullable();
            $table->string('calidad_ocupante')->nullable();
            $table->string('arrendador_nombre')->nullable();
            $table->string('arrendador_telefono')->nullable();
            $table->timestamps();
        });

        Schema::create('inquilinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_habitacional_id')->constrained('unidades_habitacionales')->cascadeOnDelete();
            $table->string('nombre_completo')->nullable();
            $table->string('documento')->nullable();
            $table->string('telefono')->nullable();
            $table->unsignedSmallInteger('num_personas')->nullable();
            $table->string('arrendador_nombre')->nullable();
            $table->string('relacion_arrendador')->nullable();
            $table->decimal('valor_arriendo', 12, 2)->nullable();
            $table->string('tiempo_viviendo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquilinos');
        Schema::dropIfExists('unidades_habitacionales');
        Schema::dropIfExists('encargados');
        Schema::dropIfExists('propietarios');
        Schema::dropIfExists('inmuebles');
    }
};
