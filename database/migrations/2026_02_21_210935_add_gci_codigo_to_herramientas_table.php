<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('herramientas', function (Blueprint $table) {
        $table->string('gci_codigo')->nullable()->after('codigo');
    });
}

public function down()
{
    Schema::table('herramientas', function (Blueprint $table) {
        $table->dropColumn('gci_codigo');
    });
}
};
