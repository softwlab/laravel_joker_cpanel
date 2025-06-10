<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeprecatedApiUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deprecated_api_usage', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->string('method', 10);
            $table->string('ip_hash', 64); // SHA-256 hash
            $table->string('user_agent_hash', 64)->nullable();
            $table->string('api_key_hash', 64);
            $table->timestamp('created_at');
            
            // Índices para consultas eficientes no relatório
            $table->index('endpoint');
            $table->index('api_key_hash');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deprecated_api_usage');
    }
}
