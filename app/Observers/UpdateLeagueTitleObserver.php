<?php

namespace App\Observers;

use App\Models\LeagueMatch;

final class UpdateLeagueTitleObserver
{
    public function created(LeagueMatch $leagueMatch): void
    {
        $leagueMatch->league_name = $leagueMatch->league->name;
        $leagueMatch->saveQuietly();
    }
}
