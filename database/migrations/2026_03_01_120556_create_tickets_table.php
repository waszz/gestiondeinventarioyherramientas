<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->text('descripcion');

            // Usuario que crea el ticket
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Funcionario asignado
            $table->foreignId('funcionario_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('sector')->nullable();
            $table->string('tipo_rotura')->nullable();
            $table->string('lugar_especifico')->nullable();

            $table->string('status')->default('abierto');
            $table->string('causa')->nullable();
            $table->string('prioridad')->default('media');

            $table->string('categoria')->nullable();
            $table->string('proyecto')->nullable();

            $table->text('detalles')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};