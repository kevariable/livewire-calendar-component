<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\League>
 */
final class LeagueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\League>
     */
    public function seed(): Collection
    {
        $sequences = [
            'NBA',
            'Pro B',
            'Basketligaen',
        ];

        return $this->sequence($sequences)->createMany(count($sequences));
    }
}
