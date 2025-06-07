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
        Schema::create('link_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('link_groups')->onDelete('cascade');
            $table->string('title');
            $table->string('url', 1000);
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_group_items');
    }
};
