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
            $table->string('cnpj')->nullable()->after('cpf');
            $table->string('email')->nullable()->after('cnpj');
            $table->string('dni')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('informacoes_bancarias', function (Blueprint $table) {
            $table->dropColumn(['cnpj', 'email', 'dni']);
        });
    }
};
