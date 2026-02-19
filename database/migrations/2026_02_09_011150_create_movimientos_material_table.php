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
       Schema::create('movimientos_material', function (Blueprint $table) {
    $table->id();

    $table->foreignId('material_id')
        ->constrained('materiales')
        ->cascadeOnDelete();

    $table->enum('tipo', [
        'entrada',   // compra
        'salida',    // uso
        'rotura',
        'ajuste'
    ]);

    $table->integer('cantidad');

    $table->string('motivo')->nullable(); // Ej: mantenimiento tablero
    $table->string('usuario')->nullable(); // quien retiró

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_material');
    }
};
