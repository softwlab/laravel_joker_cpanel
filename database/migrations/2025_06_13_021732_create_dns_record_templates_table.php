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
        Schema::create('dns_record_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dns_record_id');
            $table->integer('bank_template_id');
            $table->string('path_segment')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->unique(['dns_record_id', 'bank_template_id', 'path_segment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_record_templates');
    }
};
