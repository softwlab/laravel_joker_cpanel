<?php

namespace Tests\Feature;

use App\Models\LinkGroupItem;
use App\Models\LinkGroup;
use App\Models\Usuario;
use App\Models\Visitante;
use App\Models\DnsRecord;
use App\Console\Commands\MigrateLinksToDnsRecords;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class MigrateLinksToDnsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    
    /**
     * Usuário para teste
     * @var \App\Models\Usuario
     */
    protected $usuario;
    
    /**
     * Grupo de links para teste
     * @var \App\Models\LinkGroup
     */
    protected $linkGroup;
    
    /**
     * Item de link para teste
     * @var \App\Models\LinkGroupItem
     */
    protected $linkItem;
    
    /**
     * Registro DNS para teste
     * @var \App\Models\DnsRecord
     */
    protected $dnsRecord;

    /**
     * Prepara o ambiente de teste
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário de teste
        $this->usuario = Usuario::factory()->create();
        
        // Criar grupo de links para o teste
        $this->linkGroup = LinkGroup::factory()->create([
            'usuario_id' => $this->usuario->id,
            'nome' => 'Grupo de Teste',
            'ativo' => true
        ]);
        
        // Criar itens de link para o teste
        $this->linkItem = LinkGroupItem::factory()->create([
            'link_group_id' => $this->linkGroup->id,
            'nome' => 'Link de Teste',
            'url' => 'https://teste.com',
            'ativo' => true
        ]);
        
        // Criar registro DNS para o teste
        $this->dnsRecord = DnsRecord::factory()->create([
            'usuario_id' => $this->usuario->id,
            'name' => 'teste.dominio.com',
            'type' => 'A',
            'content' => '192.168.1.1'
        ]);
    }

    /**
     * Testa se visitantes com link_id são migrados corretamente
     */
    public function testMigracaoVisitantesComLinkId()
    {
        // Criar visitantes usando o sistema antigo (com link_id)
        $visitanteCount = 10;
        for ($i = 0; $i < $visitanteCount; $i++) {
            Visitante::factory()->create([
                'usuario_id' => $this->usuario->id,
                'link_id' => $this->linkItem->id,
                'ip' => $this->faker->ipv4,
                'user_agent' => $this->faker->userAgent,
                'migrated_to_dns' => false,
                'dns_record_id' => null
            ]);
        }
        
        // Verificar que existem visitantes não migrados
        $this->assertEquals($visitanteCount, Visitante::where('migrated_to_dns', false)->count());
        
        // Executar a migração
        $exitCode = Artisan::call('migrate:links-to-dns');
        $this->assertEquals(0, $exitCode, 'O comando deve ser executado com sucesso');
        
        // Verificar que todos os visitantes foram migrados
        $this->assertEquals(0, Visitante::where('migrated_to_dns', false)->count());
        $this->assertEquals($visitanteCount, Visitante::where('migrated_to_dns', true)->count());
        
        // Verificar que os visitantes têm um dns_record_id válido
        $this->assertEquals($visitanteCount, Visitante::whereNotNull('dns_record_id')->count());
    }
    
    /**
     * Testa se visitantes já migrados não são migrados novamente
     */
    public function testVisitantesJaMigradosNaoSaoMigradosNovamente()
    {
        // Criar alguns visitantes já migrados
        $visitanteMigrado = Visitante::factory()->create([
            'usuario_id' => $this->usuario->id,
            'link_id' => $this->linkItem->id,
            'dns_record_id' => $this->dnsRecord->id,
            'migrated_to_dns' => true
        ]);
        
        // Criar alguns visitantes não migrados
        $visitanteNaoMigrado = Visitante::factory()->create([
            'usuario_id' => $this->usuario->id,
            'link_id' => $this->linkItem->id,
            'dns_record_id' => null,
            'migrated_to_dns' => false
        ]);
        
        // Registrar o DNS record_id para checar depois
        $originalDnsRecordId = $visitanteMigrado->dns_record_id;
        
        // Executar a migração
        Artisan::call('migrate:links-to-dns');
        
        // Recarregar os modelos
        $visitanteMigrado->refresh();
        $visitanteNaoMigrado->refresh();
        
        // Verificar que o visitante já migrado mantém o dns_record_id original
        $this->assertEquals($originalDnsRecordId, $visitanteMigrado->dns_record_id);
        
        // Verificar que o visitante não migrado agora tem um dns_record_id
        $this->assertNotNull($visitanteNaoMigrado->dns_record_id);
        $this->assertTrue($visitanteNaoMigrado->migrated_to_dns);
    }
    
    /**
     * Testa se o comando lida corretamente com situações de erro
     */
    public function testComandoLidaComErros()
    {
        // Criar um visitante com link_id que não existe
        $linkIdInvalido = 9999;
        $visitanteErro = Visitante::factory()->create([
            'usuario_id' => $this->usuario->id,
            'link_id' => $linkIdInvalido, // ID que não existe
            'dns_record_id' => null,
            'migrated_to_dns' => false
        ]);
        
        // Executar a migração
        $exitCode = Artisan::call('migrate:links-to-dns');
        
        // A migração deve completar mesmo com erros
        $this->assertEquals(0, $exitCode);
        
        // Verificar que o visitante com erro não foi migrado
        $visitanteErro->refresh();
        $this->assertFalse($visitanteErro->migrated_to_dns);
        $this->assertNull($visitanteErro->dns_record_id);
    }
}
