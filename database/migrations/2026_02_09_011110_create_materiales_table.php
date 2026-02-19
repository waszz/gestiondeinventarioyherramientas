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
       Schema::create('materiales', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('codigo_referencia')->unique();
    $table->integer('stock_actual')->default(0);
    $table->integer('stock_minimo')->default(0);
    $table->boolean('material_esencial')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
