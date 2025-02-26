<?php

use App\Actions\Calendar\GenerateMonthGridAction;
use App\Data\MonthGridData;
use App\Data\MonthGridMetaData;
use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Team;
use Illuminate\Support\Collection;

test(description: 'Generates correct month grid without filters')
    ->defer(function () {
        $firstDayOfGrid = now()->startOfMonth();
        $lastDayOfGrid = $firstDayOfGrid->clone()->endOfMonth();

        $startsAt = $firstDayOfGrid->clone();
        $endsAt = $startsAt->clone()->addDays(value: 5);

        $weeks = floor(abs($firstDayOfGrid->diffInWeeks($lastDayOfGrid)) + 1);

        $league = League::factory()
            ->createOne();

        $team1 = Team::factory()
            ->for($league)
            ->createOne();

        $team2 = Team::factory()
            ->for($league)
            ->createOne();

        LeagueMatch::factory()
            ->for($team1, relationship: 'teamHome')
            ->for($team2, relationship: 'teamAway')
            ->for($league)
            ->createOne(['match_date' => $startsAt->toDateString()]);

        LeagueMatch::factory()
            ->for($team1, relationship: 'teamHome')
            ->for($team2, relationship: 'teamAway')
            ->for($league)
            ->createOne(['match_date' => $endsAt->toDateString()]);

        $action = resolve(name: GenerateMonthGridAction::class);
        $monthGrid = $action->execute(
            new MonthGridMetaData(
                firstDayOfGrid: $firstDayOfGrid,
                lastDayOfGrid: $lastDayOfGrid,
                league: null,
                team: null,
            )
        );

        expect($monthGrid)
            ->toBeInstanceOf(Collection::class)
            ->and($monthGrid->flatten())->each(fn ($item) => $item->toBeInstanceOf(MonthGridData::class))
            ->and($monthGrid)->toHaveCount(count: $weeks);

        $matchDates = $monthGrid
            ->flatten()
            ->filter(fn (MonthGridData $data) => $data->events->isNotEmpty())
            ->map(fn (MonthGridData $data) => $data->day->toDateString());

        expect($matchDates->toArray())->toContain($startsAt->toDateString(), $endsAt->toDateString());
    });

test(description: 'Generates correct month grid with league filters')
    ->defer(function () {
        $firstDayOfGrid = now()->startOfMonth();
        $lastDayOfGrid = $firstDayOfGrid->clone()->endOfMonth();

        $league = League::factory()
            ->createOne();

        $team1 = Team::factory()
            ->for($league)
            ->createOne();

        $leagueMatch = LeagueMatch::factory()
            ->for($team1, relationship: 'teamHome')
            ->for($league)
            ->createOne([
                'match_date' => fake()->dateTimeBetween(
                    startDate: $firstDayOfGrid->toDateTimeString(),
                    endDate: $lastDayOfGrid->toDateTimeString()
                ),
            ]);

        $leagueMatch2 = LeagueMatch::factory()
            ->for($team1, relationship: 'teamHome')
            ->for($league)
            ->createOne([
                'match_date' => fake()->dateTimeBetween(
                    startDate: $firstDayOfGrid->toDateTimeString(),
                    endDate: $lastDayOfGrid->toDateTimeString()
                ),
            ]);

        $action = resolve(name: GenerateMonthGridAction::class);
        $monthGrid = $action->execute(
            new MonthGridMetaData(
                firstDayOfGrid: $firstDayOfGrid,
                lastDayOfGrid: $lastDayOfGrid,
                league: $league->id,
                team: null,
            )
        );

        $events = $monthGrid
            ->pluck(value: '*.events.*.match_date')
            ->flatten()
            ->map(fn ($event) => $event->toDateString());

        expect($events->toArray())
            ->toHaveCount(count: 2)
            ->toContain($leagueMatch->match_date->toDateString())
            ->toContain($leagueMatch2->match_date->toDateString());
    });

test(description: 'Generates correct month grid with team filters')
    ->defer(function () {
        $firstDayOfGrid = now()->startOfMonth();
        $lastDayOfGrid = $firstDayOfGrid->clone()->endOfMonth();

        $league = League::factory()
            ->createOne();

        $team1 = Team::factory()
            ->for($league)
            ->createOne();

        $leagueMatch = LeagueMatch::factory()
            ->for($team1, relationship: 'teamHome')
            ->for($league)
            ->createOne([
                'match_date' => fake()->dateTimeBetween(
                    startDate: $firstDayOfGrid->toDateTimeString(),
                    endDate: $lastDayOfGrid->toDateTimeString()
                ),
            ]);

        $leagueMatch2 = LeagueMatch::factory()
            ->for($league)
            ->createOne([
                'match_date' => fake()->dateTimeBetween(
                    startDate: $firstDayOfGrid->toDateTimeString(),
                    endDate: $lastDayOfGrid->toDateTimeString()
                ),
            ]);

        $action = resolve(name: GenerateMonthGridAction::class);
        $monthGrid = $action->execute(
            new MonthGridMetaData(
                firstDayOfGrid: $firstDayOfGrid,
                lastDayOfGrid: $lastDayOfGrid,
                league: $league->id,
                team: $team1->id,
            )
        );

        $events = $monthGrid
            ->pluck(value: '*.events.*.match_date')
            ->flatten()
            ->map(fn ($event) => $event->toDateString());

        expect($events->toArray())
            ->toHaveCount(count: 1)
            ->toContain($leagueMatch->match_date->toDateString())
            ->not()->toContain($leagueMatch2->match_date->toDateString());
    });
