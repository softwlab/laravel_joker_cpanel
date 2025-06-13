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
        Schema::table('cloudflare_domains', function (Blueprint $table) {
            $table->foreign(['external_api_id'], null)->references(['id'])->on('external_apis')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cloudflare_domains', function (Blueprint $table) {
            $table->dropForeign();
        });
    }
};
