<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Esta tabela temporária é usada para armazenar informações durante o processo
     * de migração do sistema legado de links para o novo sistema DNS.
     * Os registros aqui serão posteriormente sincronizados com a Cloudflare.
     */
    public function up(): void
    {
        Schema::create('migration_temp_dns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_id')->nullable()->comment('ID do link legado');
            $table->string('name')->comment('Nome do link original');
            $table->string('url')->comment('URL alvo do link');
            $table->boolean('active')->default(true);
            $table->boolean('needs_sync')->default(true)->comment('Indica se o registro precisa ser sincronizado');
            $table->unsignedBigInteger('dns_record_id')->nullable()->comment('ID final do registro DNS após sincronização');
            $table->text('sync_error')->nullable()->comment('Erros durante a sincronização');
            $table->timestamps();
            
            $table->index('link_id');
            $table->index('needs_sync');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('migration_temp_dns');
    }
};
