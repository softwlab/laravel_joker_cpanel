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
        Schema::create('dns_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('external_api_id');
            $table->integer('bank_id')->nullable()->index();
            $table->integer('bank_template_id')->nullable()->index();
            $table->integer('link_group_id')->nullable()->index();
            $table->integer('user_id')->nullable()->index();
            $table->string('record_type');
            $table->string('name');
            $table->text('content');
            $table->integer('ttl')->default(3600);
            $table->integer('priority')->nullable();
            $table->string('status')->default('active');
            $table->text('extra_data')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index(['external_api_id', 'record_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_records');
    }
};
