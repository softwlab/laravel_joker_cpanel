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
            $table->unsignedBigInteger('dns_record_id')->nullable()->after('link_id');
            $table->foreign('dns_record_id')->references('id')->on('dns_records')->onDelete('set null');
            
            // Adiciona um índice para melhorar a performance
            $table->index('dns_record_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitantes', function (Blueprint $table) {
            $table->dropForeign(['dns_record_id']);
            $table->dropIndex(['dns_record_id']);
            $table->dropColumn('dns_record_id');
        });
    }
};
