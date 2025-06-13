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
        Schema::table('user_configs', function (Blueprint $table) {
            $table->foreign(['record_id'], null)->references(['id'])->on('dns_records')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['template_id'], null)->references(['id'])->on('bank_templates')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'], null)->references(['id'])->on('usuarios')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_configs', function (Blueprint $table) {
            $table->dropForeign();
            $table->dropForeign();
            $table->dropForeign();
        });
    }
};
