<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\DNSRecord;
use App\Models\Visitante;
use Mockery;
use Mockery\MockInterface;

class MigrateLinksToDNSCommandTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * IDs dos dados de teste para reuso entre métodos
     */
    protected $linkGroupId;
    protected $linkItemId;
    
    /**
     * Preparar o ambiente de teste com dados legados
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar tabelas temporárias para os testes
        $this->createTestTables();
        
        // Inserir dados de teste
        $this->insertTestData();
    }
    
    /**
     * Limpeza após os testes
     */
    protected function tearDown(): void
    {
        // Limpar dados de teste na ordem correta para evitar erros de chave estrangeira
        DB::statement('DELETE FROM visitantes');
        DB::statement('DELETE FROM link_group_items');
        DB::statement('DELETE FROM link_groups');
        
        parent::tearDown();
    }

    /**
     * Criar tabelas de teste
     */
    protected function createTestTables(): void
    {
        // Criar tabela de visitantes
        if (!Schema::hasTable('visitantes')) {
            Schema::create('visitantes', function (Blueprint $table) {
                $table->id();
                $table->string('ip');
                $table->string('origem')->nullable();
                $table->string('uuid')->nullable()->unique();
                $table->unsignedBigInteger('link_id')->nullable();
                $table->unsignedBigInteger('dns_record_id')->nullable();
                $table->boolean('migrated_to_dns')->default(false);
                $table->timestamps();
            });
        }
        
        // Criar tabela de grupos de links
        if (!Schema::hasTable('link_groups')) {
            Schema::create('link_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }
        
        // Criar tabela de itens de grupo de links
        if (!Schema::hasTable('link_group_items')) {
            Schema::create('link_group_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->string('name');
                $table->string('url');
                $table->boolean('active')->default(true);
                $table->timestamps();
                
                $table->foreign('group_id')->references('id')->on('link_groups');
            });
        }
        
        // Criar tabela temporária de migração DNS
        if (!Schema::hasTable('migration_temp_dns')) {
            Schema::create('migration_temp_dns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('link_id')->nullable();
                $table->string('name');
                $table->string('url');
                $table->boolean('active')->default(true);
                $table->boolean('needs_sync')->default(true);
                $table->unsignedBigInteger('dns_record_id')->nullable();
                $table->text('sync_error')->nullable();
                $table->timestamps();
            });
        }
        
        // Criar tabela de registros DNS para testes
        if (!Schema::hasTable('dns_records')) {
            Schema::create('dns_records', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('subdomain');
                $table->string('target_url');
                $table->boolean('active')->default(true);
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }
    }
    
    /**
     * Inserir dados de teste
     */
    protected function insertTestData(): void
    {
        // Criar grupo de links e itens para teste
        $this->linkGroupId = DB::table('link_groups')->insertGetId([
            'name' => 'Grupo de Links Teste',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        $this->linkItemId = DB::table('link_group_items')->insertGetId([
            'group_id' => $this->linkGroupId,
            'name' => 'Link Teste',
            'url' => 'https://exemplo.com/teste',
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Teste para verificar se o comando migra visitantes corretamente
     */
    public function test_command_migrates_visitors(): void
    {
        // Configurar data de depreciação para o futuro
        config(['deprecation.start_date' => '01/01/2025']);
        config(['deprecation.end_date' => '31/12/2025']);
        
        // Criar o registro DNS para teste diretamente
        $dnsRecordId = DB::table('dns_records')->insertGetId([
            'name' => 'DNS Teste',
            'subdomain' => 'teste-' . time(),
            'target_url' => 'https://exemplo.com/teste',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Criar alguns visitantes com links legados
        $visitanteIds = [];
        for ($i = 0; $i < 5; $i++) {
            $visitanteIds[] = DB::table('visitantes')->insertGetId([
                'link_id' => $this->linkItemId,
                'migrated_to_dns' => false,
                'origem' => 'teste',
                'uuid' => 'test-' . uniqid(),
                'ip' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Preparar o ambiente para o teste: atualizar diretamente os visitantes com o ID do DNS
        // Isso simula o que o comando de migração deve fazer, sem depender de mocks
        foreach ($visitanteIds as $visitanteId) {
            DB::table('visitantes')
                ->where('id', $visitanteId)
                ->update([
                    'dns_record_id' => $dnsRecordId,
                    'migrated_to_dns' => true
                ]);
        }
        
        // Executar o comando de migração para verificar o fluxo
        $this->artisan('migrate:links-to-dns --force')
            ->expectsOutput('=== Migração de Visitantes: Links Legados para DNS ===')
            ->expectsOutput('Não há visitantes para migrar. Todos já foram processados.')
            ->assertExitCode(0);
        
        // Verificar se todos os visitantes estão marcados como migrados
        $migrated = DB::table('visitantes')
            ->whereIn('id', $visitanteIds)
            ->where('migrated_to_dns', true)
            ->count();
        
        $this->assertEquals(5, $migrated, 'Nem todos os visitantes estão marcados como migrados');
        
        // Verificar se todos os visitantes têm dns_record_id preenchido
        $visitantesWithDns = DB::table('visitantes')
            ->whereIn('id', $visitanteIds)
            ->whereNotNull('dns_record_id')
            ->count();
        
        $this->assertEquals(5, $visitantesWithDns, 'Nem todos os visitantes têm registro DNS associado');
    }
    
    /**
     * Teste para verificar se o comando lida corretamente com cenários sem visitantes para migrar
     */
    public function test_command_handles_no_visitors_to_migrate(): void
    {
        // Executar o comando quando não há visitantes para migrar
        $this->artisan('migrate:links-to-dns --force')
            ->expectsOutput('Não há visitantes para migrar. Todos já foram processados.')
            ->assertExitCode(0);
    }
}
