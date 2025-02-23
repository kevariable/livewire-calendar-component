<?php

namespace Database\Factories;

use App\Models\League;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\League>
 */
final class LeagueMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'league_id' => League::factory(),
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'match_date' => fake()->date(),
            'location' => fake()->city(),
        ];
    }
}
