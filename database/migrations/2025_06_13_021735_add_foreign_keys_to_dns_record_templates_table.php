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
        Schema::table('dns_record_templates', function (Blueprint $table) {
            $table->foreign(['bank_template_id'], null)->references(['id'])->on('bank_templates')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['dns_record_id'], null)->references(['id'])->on('dns_records')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dns_record_templates', function (Blueprint $table) {
            $table->dropForeign();
            $table->dropForeign();
        });
    }
};
