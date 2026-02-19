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
        Schema::create('historial_herramientas', function (Blueprint $table) {
    $table->id();

    $table->foreignId('herramienta_id')->constrained()->cascadeOnDelete();
    $table->string('nombre');
    $table->string('codigo');

    $table->string('tipo'); 
    // prestamo | devolucion | fuera_servicio | prestamo_multiple | devolucion_multiple | restaurado

    $table->integer('cantidad')->default(0);

    $table->string('funcionario')->nullable();

    $table->text('detalle')->nullable();
    $table->text('observacion')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_herramientas');
    }
};
