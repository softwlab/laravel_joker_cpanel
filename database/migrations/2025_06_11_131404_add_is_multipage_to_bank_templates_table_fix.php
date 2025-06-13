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
        Schema::table('bank_templates', function (Blueprint $table) {
            // Adicionando a coluna is_multipage que está faltando
            if (!Schema::hasColumn('bank_templates', 'is_multipage')) {
                $table->boolean('is_multipage')->default(false)->after('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_templates', function (Blueprint $table) {
            if (Schema::hasColumn('bank_templates', 'is_multipage')) {
                $table->dropColumn('is_multipage');
            }
        });
    }
};
