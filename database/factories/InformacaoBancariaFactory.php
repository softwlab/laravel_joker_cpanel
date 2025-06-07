<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InformacaoBancaria>
 */
class InformacaoBancariaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'visitante_uuid' => function () {
                return \App\Models\Visitante::factory()->create()->uuid;
            },
            'data' => $this->faker->date(),
            'agencia' => $this->faker->numerify('####'),
            'conta' => $this->faker->numerify('#####-#'),
            'cpf' => $this->faker->numerify('###.###.###-##'),
            'nome_completo' => $this->faker->name(),
            'telefone' => $this->faker->phoneNumber(),
            'informacoes_adicionais' => json_encode([
                'valor' => $this->faker->randomFloat(2, 100, 10000),
                'motivo' => $this->faker->sentence(3),
                'status' => $this->faker->randomElement(['pendente', 'processado', 'cancelado'])
            ])
        ];
    }
}
