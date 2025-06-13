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
        Schema::create('user_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index('user_configs_usuario_id_index');
            $table->text('config_json')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('template_id')->nullable()->index();
            $table->integer('record_id')->nullable()->index();
            $table->text('config')->nullable();

            $table->unique(['user_id', 'template_id', 'record_id'], 'user_template_record_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_configs');
    }
};
