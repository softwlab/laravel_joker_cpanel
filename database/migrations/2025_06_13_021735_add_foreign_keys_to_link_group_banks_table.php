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
        Schema::table('link_group_banks', function (Blueprint $table) {
            $table->foreign(['bank_id'], null)->references(['id'])->on('banks')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['link_group_id'], null)->references(['id'])->on('link_groups')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('link_group_banks', function (Blueprint $table) {
            $table->dropForeign();
            $table->dropForeign();
        });
    }
};
