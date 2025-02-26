<?php

namespace App\Builders;

use App\Models\League;
use App\Models\LeagueMatch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class LeagueMatchBuilder extends Builder
{
    /**
     * @return Collection<int, array<int, string>>
     */
    public function options(): Collection
    {
        return $this->get()->mapWithKeys(
            fn (League $league) => [
                $league->id => $league->name,
            ]
        );
    }

    public function orderByDefault(): self
    {
        return $this->orderByRaw("
            CASE 
                WHEN league_name = 'NBA - G League' THEN 1
                WHEN league_name = 'Pro B' THEN 2
                WHEN league_name = 'Basketligaen' THEN 3
                ELSE 4
            END
        ")->latest(column: 'match_date');
    }

    public function whereMatchDateBy(string $year, string $month, string $day): self
    {
        return $this->whereDate(column: 'match_date', operator: '=', value: Carbon::createFromDate($year, $month, $day)->toDateString());
    }

    public function groupByMatchDateBetween(Carbon $startsAt, Carbon $endsAt): Collection
    {
        return $this
            ->whereBetween(column: 'match_date', values: [$startsAt->toDateString(), $endsAt->toDateString()])
            ->get()
            ->groupBy(groupBy: fn (LeagueMatch $leagueMatch) => $leagueMatch->match_date->toDateString());
    }

    public function applyTeamFilter(?string $team): self
    {
        return $this->when(
            value: filled($team),
            callback: fn (LeagueMatchBuilder $query) => $query->where(
                fn (LeagueMatchBuilder $query) => $query
                    ->where(column: 'home_team_id', operator: '=', value: $team)
                    ->orWhere(column: 'away_team_id', operator: '=', value: $team)
            )
        );
    }

    public function applyLeagueFilter(mixed $league): self
    {
        return $this->when(
            value: filled($league),
            callback: fn (LeagueMatchBuilder $query) => $query->where('league_id', $league)
        );
    }

    public function orderByDefaultWhen(bool $condition): self
    {
        return $this->when(
            value: $condition,
            callback: fn (LeagueMatchBuilder $query) => $query->orderByDefault()
        );
    }
}
