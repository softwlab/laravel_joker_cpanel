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
        Schema::table('informacoes_bancarias', function (Blueprint $table) {
            $table->foreign(['visitante_uuid'], null)->references(['uuid'])->on('visitantes')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('informacoes_bancarias', function (Blueprint $table) {
            $table->dropForeign();
        });
    }
};
