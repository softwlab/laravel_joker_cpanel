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
            $table->increments('id');
            $table->integer('bank_template_id')->index();
            $table->string('name');
            $table->string('field_key');
            $table->string('field_type')->default('text');
            $table->string('placeholder')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->text('options')->nullable();
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
