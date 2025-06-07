<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LinkGroupItem>
 */
class LinkGroupItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => \App\Models\LinkGroup::factory(),
            'title' => $this->faker->words(2, true),
            'url' => $this->faker->url,
            'icon' => 'fa fa-link',
            'order' => $this->faker->numberBetween(1, 10),
            'active' => true
        ];
    }
}
