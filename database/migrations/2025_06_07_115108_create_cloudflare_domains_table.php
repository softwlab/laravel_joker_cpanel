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
            $table->id();
            $table->foreignId('external_api_id')->constrained()->onDelete('cascade');
            $table->string('zone_id')->index();
            $table->string('name')->index();
            $table->string('status')->default('active');
            $table->boolean('paused')->default(false);
            $table->boolean('is_ghost')->default(false);
            $table->json('name_servers')->nullable();
            $table->integer('records_count')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
            
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
