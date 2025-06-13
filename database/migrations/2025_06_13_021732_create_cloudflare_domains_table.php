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
        Schema::create('cloudflare_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('external_api_id');
            $table->string('zone_id')->index();
            $table->string('name');
            $table->string('status')->default('active');
            $table->boolean('is_ghost')->default(false);
            $table->text('name_servers')->nullable();
            $table->integer('records_count')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->unique(['external_api_id', 'zone_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloudflare_domains');
    }
};
