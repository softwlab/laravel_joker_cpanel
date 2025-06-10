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
        Schema::table('visitantes', function (Blueprint $table) {
            // Verificar se as colunas existem antes de tentar removê-las
            if (Schema::hasColumn('visitantes', 'link_id')) {
                $table->dropColumn('link_id');
            }
            
            if (Schema::hasColumn('visitantes', 'migrated_to_dns')) {
                $table->dropColumn('migrated_to_dns');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitantes', function (Blueprint $table) {
            $table->foreignId('link_id')->nullable();
            $table->boolean('migrated_to_dns')->default(false);
        });
    }
};
