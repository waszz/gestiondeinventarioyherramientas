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
    Schema::table('herramienta_prestamos', function (Blueprint $table) {
        $table->string('observaciones')->nullable()->after('estado');
    });
}

public function down()
{
    Schema::table('herramienta_prestamos', function (Blueprint $table) {
        $table->dropColumn('observaciones');
    });
}
};
