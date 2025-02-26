<?php

namespace App\Data;

use App\Models\LeagueMatch;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final readonly class MonthGridData
{
    public function __construct(
        public Carbon $day,

        /** @var Collection<int, LeagueMatch> LeagueMatch */
        public Collection $events
    ) {}
}
