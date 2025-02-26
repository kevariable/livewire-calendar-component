<?php

namespace App\Data;

use Carbon\Carbon;

final readonly class MonthGridMetaData
{
    public function __construct(
        public Carbon $firstDayOfGrid,
        public Carbon $lastDayOfGrid,
        public ?string $league,
        public ?string $team,
    ) {}
}
