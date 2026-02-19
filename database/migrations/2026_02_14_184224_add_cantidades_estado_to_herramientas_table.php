<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('herramientas', function (Blueprint $table) {
            $table->integer('cantidad_disponible')->default(0);
            $table->integer('cantidad_prestamo')->default(0);
            $table->integer('cantidad_fuera_servicio')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('herramientas', function (Blueprint $table) {
            $table->dropColumn(['cantidad_disponible', 'cantidad_prestamo', 'cantidad_fuera_servicio']);
        });
    }
};

