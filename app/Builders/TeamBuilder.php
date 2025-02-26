<?php

namespace App\Builders;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class TeamBuilder extends Builder
{
    /**
     * @return Collection<int, array<int, string>>
     */
    public function options(): Collection
    {
        return $this->get()->mapWithKeys(
            fn (Team $league) => [
                $league->id => $league->name,
            ]
        );
    }
}
