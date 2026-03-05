<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('historial_estado_funcionarios', function (Blueprint $table) {
        $table->id();
        $table->foreignId('funcionario_id')->constrained()->onDelete('cascade');
        $table->string('estado'); // disponible / no_disponible / falta
        $table->timestamp('inicio');
        $table->timestamp('fin')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_estado_funcionarios');
    }
};
