<?php

namespace App\Builders;

use App\Models\League;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class LeagueBuilder extends Builder
{
    /**
     * @return Collection<int, array<int, string>>
     */
    public function options(): Collection
    {
        return $this->get()->mapWithKeys(
            fn (League $league) => [
                $league->id => $league->name
            ]
        );
    }
}