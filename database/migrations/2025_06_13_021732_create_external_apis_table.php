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
        Schema::create('external_apis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_link_api');
            $table->string('key_external_api');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('json')->nullable();
            $table->string('type');
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->text('config')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_apis');
    }
};
