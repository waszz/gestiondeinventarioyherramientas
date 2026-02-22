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
        Schema::table('materiales', function (Blueprint $table) {
            $table->foreignId('tipo_material_id')      // columna para el tipo
                  ->nullable()                          // puede ser null
                  ->constrained('tipo_materials')      // apunta a tabla tipo_materials
                  ->nullOnDelete()                      // si borrás un tipo, deja null
                  ->after('codigo_referencia');         // posición en la tabla
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materiales', function (Blueprint $table) {
            $table->dropForeign(['tipo_material_id']);
            $table->dropColumn('tipo_material_id');
        });
    }
};
