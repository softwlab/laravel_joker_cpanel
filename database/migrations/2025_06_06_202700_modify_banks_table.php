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
        Schema::table('banks', function (Blueprint $table) {
            $table->foreignId('bank_template_id')->nullable()->after('id')->constrained('bank_templates');
            $table->json('field_values')->nullable()->after('links');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropForeign(['bank_template_id']);
            $table->dropColumn('bank_template_id');
            $table->dropColumn('field_values');
        });
    }
};
