<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visitante>
 */
class VisitanteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'usuario_id' => \App\Models\Usuario::factory(),
            'ip' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'referrer' => $this->faker->url
        ];
    }
}
