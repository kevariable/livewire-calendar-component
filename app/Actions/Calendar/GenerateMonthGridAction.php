<?php

namespace App\Actions\Calendar;

use App\Data\MonthGridData;
use App\Data\MonthGridMetaData;
use App\Models\LeagueMatch;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final readonly class GenerateMonthGridAction
{
    /**
     * @param \App\Data\MonthGridMetaData $data
     * @return \Illuminate\Support\Collection<int, MonthGridData[]>
     */
    public function execute(MonthGridMetaData $data): Collection
    {
        /** @var \Illuminate\Support\Collection<int, Carbon> $monthGrid */
        $monthGrid = collect();

        $currentDay = $data->firstDayOfGrid->clone();

        while (! $currentDay->greaterThan($data->lastDayOfGrid)) {
            $monthGrid->push($currentDay->clone());

            $currentDay->addDay();
        }

        $startsAt = $monthGrid->first();
        $endsAt = $monthGrid->last();

        $leagueMatches = LeagueMatch::query()
            ->applyLeagueFilter($data->league)
            ->applyTeamFilter($data->team)
            ->groupByMatchDateBetween($startsAt, $endsAt);

        $mapper = fn (Carbon $dateTime) => new MonthGridData(
            day: $dateTime,
            events: $leagueMatches[$dateTime->toDateString()] ?? collect()
        );

        return $monthGrid->map($mapper)->chunk(size: CarbonInterface::DAYS_PER_WEEK);
    }
}
