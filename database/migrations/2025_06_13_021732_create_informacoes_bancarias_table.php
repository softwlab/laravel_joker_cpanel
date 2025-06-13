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
        Schema::create('informacoes_bancarias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('visitante_uuid')->index();
            $table->date('data')->nullable();
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->string('cpf')->nullable()->index();
            $table->string('nome_completo')->nullable();
            $table->string('telefone')->nullable();
            $table->text('informacoes_adicionais')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informacoes_bancarias');
    }
};
