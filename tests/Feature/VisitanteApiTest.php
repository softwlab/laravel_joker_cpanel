<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\LinkGroupItem;
use App\Models\LinkGroup;
use App\Models\Usuario;
use App\Models\Visitante;

class VisitanteApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Testa o registro de um novo visitante pela API.
     */
    public function test_pode_registrar_visitante(): void
    {
        // Cria um usuário, grupo e link
        $usuario = Usuario::factory()->create();
        $grupo = LinkGroup::factory()->create([
            'usuario_id' => $usuario->id
        ]);
        $link = LinkGroupItem::factory()->create([
            'group_id' => $grupo->id
        ]);

        // Cria os dados para o teste
        $payload = [
            'link_id' => $link->id,
            'ip' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'referrer' => 'https://google.com'
        ];

        // Faz a requisição para a API
        $this->withoutExceptionHandling();
        $response = $this->postJson('/api/visitantes', $payload);

        // Verifica se a resposta foi bem-sucedida
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'visitante_uuid',
                    'usuario_id'
                ]
            ]);

        // Verifica se o visitante foi criado no banco de dados
        $this->assertDatabaseHas('visitantes', [
            'link_id' => $link->id,
            'usuario_id' => $usuario->id,
            'ip' => $payload['ip']
        ]);
    }

    /**
     * Testa a validação de link_id no registro de visitante
     */
    public function test_valida_link_id_ao_registrar_visitante(): void
    {
        // Tenta registrar um visitante sem link_id
        // Certifique-se de que a rota existe, mas a validação falha
        // Para garantir que a rota existe, primeiro vamos criar um usuario e link válidos
        $usuario = Usuario::factory()->create();
        $grupo = LinkGroup::factory()->create(['usuario_id' => $usuario->id]);
        $link = LinkGroupItem::factory()->create(['group_id' => $grupo->id]);

        // Apenas para verificar se a rota existe e funciona com dados válidos
        $this->postJson('/api/visitantes', ['link_id' => $link->id]);

        // Agora testamos a validação
        $response = $this->postJson('/api/visitantes', [
            'ip' => $this->faker->ipv4,
        ]);

        // Verifica se a validação funcionou
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['link_id']);
    }

    /**
     * Testa o registro de informações bancárias
     */
    public function test_pode_registrar_informacao_bancaria(): void
    {
        // Cria um visitante
        $usuario = Usuario::factory()->create();
        $grupo = LinkGroup::factory()->create(['usuario_id' => $usuario->id]);
        $link = LinkGroupItem::factory()->create(['group_id' => $grupo->id]);
        $visitante = Visitante::factory()->create([
            'usuario_id' => $usuario->id,
            'link_id' => $link->id
        ]);

        // Cria os dados para a informação bancária
        $payload = [
            'visitante_uuid' => $visitante->uuid,
            'data' => now()->format('Y-m-d'),
            'agencia' => '1234',
            'conta' => '56789-0',
            'cpf' => '123.456.789-00',
            'nome_completo' => $this->faker->name,
            'telefone' => '(11) 98765-4321',
            'informacoes_adicionais' => [
                'valor' => 1500.00,
                'motivo' => 'Teste'
            ]
        ];

        // Faz a requisição para a API
        $this->withoutExceptionHandling();
        $response = $this->postJson('/api/informacoes-bancarias', $payload);

        // Verifica se a resposta foi bem-sucedida
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        // Verifica se a informação foi criada no banco de dados
        $this->assertDatabaseHas('informacoes_bancarias', [
            'visitante_uuid' => $visitante->uuid,
            'agencia' => $payload['agencia'],
            'cpf' => $payload['cpf']
        ]);
    }

    /**
     * Testa a validação de UUID no registro de informações bancárias
     */
    public function test_valida_visitante_uuid_ao_registrar_informacao(): void
    {
        // Primeiro cria um visitante válido para garantir que a rota existe
        $usuario = Usuario::factory()->create();
        $grupo = LinkGroup::factory()->create(['usuario_id' => $usuario->id]);
        $link = LinkGroupItem::factory()->create(['group_id' => $grupo->id]);
        $visitante = Visitante::factory()->create([
            'usuario_id' => $usuario->id,
            'link_id' => $link->id
        ]);

        // Verifica se a rota funciona com dados válidos
        $this->postJson('/api/informacoes-bancarias', [
            'visitante_uuid' => $visitante->uuid,
            'agencia' => '1234',
        ]);

        // Tenta registrar uma informação sem UUID
        $response = $this->postJson('/api/informacoes-bancarias', [
            'agencia' => '1234',
            'conta' => '56789-0',
        ]);

        // Verifica se a validação funcionou
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['visitante_uuid']);
    }
}
