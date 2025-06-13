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
        Schema::create('bank_template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('bank_templates')->onDelete('cascade');
            $table->string('field_key', 50);
            $table->string('label')->nullable();
            $table->string('placeholder')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->string('input_type')->default('text');
            $table->string('validation_rules')->nullable();
            $table->timestamps();
            
            // Índices para otimização de consultas
            $table->index('field_key');
            $table->index('order');
            $table->unique(['template_id', 'field_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_template_fields');
    }
};
