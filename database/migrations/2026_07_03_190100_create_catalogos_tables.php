<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('catalogo_opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_grupo_id')->constrained()->cascadeOnDelete();
            $table->string('valor');
            $table->string('etiqueta');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['catalogo_grupo_id', 'valor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_opciones');
        Schema::dropIfExists('catalogo_grupos');
    }
};
