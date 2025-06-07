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
        Schema::create('link_group_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_group_id')->constrained('link_groups')->onDelete('cascade');
            $table->foreignId('bank_id')->constrained('banks')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique(['link_group_id', 'bank_id']);
            $table->index('link_group_id');
            $table->index('bank_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_group_banks');
    }
};
