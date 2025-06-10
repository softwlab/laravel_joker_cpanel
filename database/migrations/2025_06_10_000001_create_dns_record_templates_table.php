<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Execute as migrações.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dns_record_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dns_record_id');
            $table->unsignedBigInteger('bank_template_id');
            $table->string('path_segment')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('dns_record_id')->references('id')->on('dns_records')->onDelete('cascade');
            $table->foreign('bank_template_id')->references('id')->on('bank_templates')->onDelete('cascade');
            
            // Garantir que não haja duplicatas
            $table->unique(['dns_record_id', 'bank_template_id']);
            
            // Garantir que path_segment seja único para cada dns_record
            $table->unique(['dns_record_id', 'path_segment']);
        });
    }

    /**
     * Reverta as migrações.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dns_record_templates');
    }
};
