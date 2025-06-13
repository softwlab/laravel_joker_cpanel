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
        Schema::table('dns_records', function (Blueprint $table) {
            $table->foreign(['user_id'], null)->references(['id'])->on('users')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['link_group_id'], null)->references(['id'])->on('link_groups')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['bank_template_id'], null)->references(['id'])->on('bank_templates')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['bank_id'], null)->references(['id'])->on('banks')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['external_api_id'], null)->references(['id'])->on('external_apis')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dns_records', function (Blueprint $table) {
            $table->dropForeign();
            $table->dropForeign();
            $table->dropForeign();
            $table->dropForeign();
            $table->dropForeign();
        });
    }
};
