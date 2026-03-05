<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('sector_id')
                  ->nullable()
                  ->after('descripcion')
                  ->constrained('sectores')
                  ->nullOnDelete();

            $table->foreignId('lugar_id')
                  ->nullable()
                  ->after('sector_id')
                  ->constrained('lugares')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['sector_id']);
            $table->dropForeign(['lugar_id']);
            $table->dropColumn(['sector_id', 'lugar_id']);
        });
    }
};