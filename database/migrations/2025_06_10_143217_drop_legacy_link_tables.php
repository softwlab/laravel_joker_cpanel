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
        // Remover as chaves estrangeiras primeiro se as tabelas existirem
        if (Schema::hasTable('link_group_items')) {
            Schema::table('link_group_items', function (Blueprint $table) {
                // Verificar se as chaves estrangeiras existem
                if (Schema::hasColumn('link_group_items', 'group_id')) {
                    // Tentativa de remover a foreign key
                    try {
                        $table->dropForeign(['group_id']);
                    } catch (\Exception $e) {
                        // Ignorar erro se a foreign key não existir
                    }
                }
            });
        }
        
        if (Schema::hasTable('link_group_banks')) {
            Schema::table('link_group_banks', function (Blueprint $table) {
                try {
                    if (Schema::hasColumn('link_group_banks', 'group_id')) {
                        $table->dropForeign(['group_id']);
                    }
                    if (Schema::hasColumn('link_group_banks', 'bank_id')) {
                        $table->dropForeign(['bank_id']);
                    }
                } catch (\Exception $e) {
                    // Ignorar erro se a foreign key não existir
                }
            });
        }
        
        // Remover as tabelas
        Schema::dropIfExists('link_group_banks');
        Schema::dropIfExists('link_group_items');
        Schema::dropIfExists('link_groups');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Criar tabela de grupos de links
        Schema::create('link_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios')
                ->onDelete('cascade');
        });
        
        // Criar tabela de itens de grupo
        Schema::create('link_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id');
            $table->string('title');
            $table->string('url');
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->foreign('group_id')
                ->references('id')
                ->on('link_groups')
                ->onDelete('cascade');
        });
        
        // Criar tabela de associação entre grupos e bancos
        Schema::create('link_group_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id');
            $table->foreignId('bank_id');
            $table->timestamps();
            
            $table->foreign('group_id')
                ->references('id')
                ->on('link_groups')
                ->onDelete('cascade');
                
            $table->foreign('bank_id')
                ->references('id')
                ->on('banks')
                ->onDelete('cascade');
        });
    }
};
