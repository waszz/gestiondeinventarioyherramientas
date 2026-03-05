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
    Schema::table('funcionarios', function (Blueprint $table) {
        $table->string('imagen')->nullable();
        $table->enum('estado', ['disponible', 'no_disponible', 'falta'])
              ->default('disponible');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            //
        });
    }
};
