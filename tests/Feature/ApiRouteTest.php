<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRouteTest extends TestCase
{
    /**
     * Testa se as rotas da API estão disponíveis.
     */
    public function test_api_routes_are_available(): void
    {
        // Verifica a rota completa - deve retornar 422 para dados inválidos, não 404
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('/api/visitantes', []);
        
        // Imprime a resposta para debug
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Content: " . $response->getContent() . "\n";
        
        // Não deve ser 404 (não encontrado) - deve ser 422 (erro de validação)
        $this->assertNotEquals(404, $response->getStatusCode(), 
            "Rota /api/visitantes retornou 404 - verifique se está registrada corretamente");
    }
}
