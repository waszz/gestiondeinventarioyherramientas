<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            // tipo de pedido
            $table->string('tipo'); // material | herramienta

            // relaciones
            $table->foreignId('materiales_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('herramientas_id')->nullable()->constrained()->nullOnDelete();

            // datos del item
            $table->string('nombre');
            $table->string('codigo')->nullable();
            $table->string('sku')->nullable();
            $table->string('numero_seguimiento')->nullable();

            $table->integer('cantidad');

            // estado del pedido
            $table->string('estado')->default('pendiente'); 
            // pendiente | enviado | completado | rechazado

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
