<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable()->after('remember_token')->constrained()->nullOnDelete();
            $table->foreignId('municipio_id')->nullable()->after('departamento_id')->constrained()->nullOnDelete();
            $table->foreignId('comuna_id')->nullable()->after('municipio_id')->constrained()->nullOnDelete();
            $table->foreignId('barrio_id')->nullable()->after('comuna_id')->constrained()->nullOnDelete();
            $table->foreignId('sector_id')->nullable()->after('barrio_id')->constrained('sectores')->nullOnDelete();
            $table->boolean('activo')->default(true)->after('sector_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sector_id');
            $table->dropConstrainedForeignId('barrio_id');
            $table->dropConstrainedForeignId('comuna_id');
            $table->dropConstrainedForeignId('municipio_id');
            $table->dropConstrainedForeignId('departamento_id');
            $table->dropColumn('activo');
        });
    }
};
