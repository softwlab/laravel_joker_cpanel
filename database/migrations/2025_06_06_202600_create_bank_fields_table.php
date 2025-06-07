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
        Schema::create('bank_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_template_id')->constrained('bank_templates')->onDelete('cascade');
            $table->string('field_name');
            $table->string('field_label');
            $table->string('field_type')->default('text');
            $table->string('placeholder')->nullable();
            $table->boolean('required')->default(true);
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index('bank_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_fields');
    }
};
