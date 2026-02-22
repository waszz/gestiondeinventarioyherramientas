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
    Schema::table('historial_herramientas', function (Blueprint $table) {
        $table->integer('cantidad_baterias')->default(0)->after('cantidad');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial_herramientas', function (Blueprint $table) {
            //
        });
    }
};
