<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $time = Benchmark::measure(function (): void {
            DB::beginTransaction();
            try {
                $matchData = $this->loadMatchData();
                $leagueData = $this->organizeMatchesByLeague($matchData);
                $leagues = $this->createLeagues(array_keys($leagueData));

                foreach ($leagueData as $leagueName => $matches) {
                    $league = $leagues[$leagueName] ?? null;

                    if (! $league) continue;

                    $teams = $this->createTeamsForLeague($league, $matches);
                    $this->createMatches($league, $teams, $matches);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });

        $this->command->info('Seed complete in '. $time/1000 .' seconds !');
    }

    /**
     * Load match data from JSON file
     *
     * @return array
     */
    private function loadMatchData(): array
    {
        /**
         * @var array<int, array{
         *      date: string,
         *      timestamp: integer,
         *      vanue: null | string,
         *      league: string,
         *      team1: string,
         *      team2: string
         *  }> $matchData
         */
        return Storage::json('matches.json');
    }

    /**
     * Organize matches by league
     *
     * @param array $matchData
     * @return array
     */
    private function organizeMatchesByLeague(array $matchData): array
    {
        /**
         * @var array<string, array{
         *     home_team_id: string,
         *     away_team_id: string,
         *     match_date: string,
         *     location: string,
         * }> $leagueData
         */
        $leagueData = [];

        foreach ($matchData as $match) {
            $leagueData[$match['league']][] = [
                'home_team_id' => $match['team1'],
                'away_team_id' => $match['team2'],
                'match_date' => $match['date'],
                'location' => $match['vanue'] ?? fake()->city(),
            ];
        }

        return $leagueData;
    }

    /**
     * Create leagues and return a collection keyed by league name
     *
     * @param array $leagueNames
     * @return Collection<League>
     */
    private function createLeagues(array $leagueNames): Collection
    {
        $leagueData = Arr::map(
            $leagueNames,
            fn (string $league) => ['name' => $league]
        );

        return League::factory()
            ->sequence(...$leagueData)
            ->createMany(count($leagueData))
            ->mapWithKeys(fn (League $league) => [
                $league->name => $league
            ]);
    }

    /**
     * Create teams for a league
     *
     * @param League $league
     * @param array $matches
     * @return Collection<\App\Models\Team>
     */
    private function createTeamsForLeague(League $league, array $matches): Collection
    {
        $teamNames = $this->extractUniqueTeamNames($matches);

        $teamData = Arr::map(
            $teamNames,
            fn (string $team) => ['name' => $team]
        );

        return $league->teams()
            ->createMany($teamData)
            ->mapWithKeys(fn (Team $team) => [
                $team->name => $team
            ]);
    }

    /**
     * Extract unique team names from matches
     *
     * @param array $matches
     * @return array
     */
    private function extractUniqueTeamNames(array $matches): array
    {
        $teamNames = [];

        foreach ($matches as $match) {
            $teamNames[] = $match['home_team_id'];
            $teamNames[] = $match['away_team_id'];
        }

        return array_unique($teamNames);
    }

    /**
     * Create matches for a league
     *
     * @param League $league
     * @param Collection<\App\Models\Team> $teams
     * @param array $matches
     * @return void
     */
    private function createMatches(League $league, Collection $teams, array $matches): void
    {
        $formattedMatches = Arr::map($matches, fn (array $match) => [
            'league_id' => $league->id,
            'league_name' => $league->name,
            'home_team_id' => $teams[$match['home_team_id']]->id,
            'away_team_id' => $teams[$match['away_team_id']]->id,
            'match_date' => Carbon::parse($match['match_date']),
            'location' => $match['location'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        LeagueMatch::query()->insert($formattedMatches);
    }
}