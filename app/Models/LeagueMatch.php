<?php

namespace App\Models;

use App\Builders\LeagueMatchBuilder;
use App\Observers\UpdateLeagueTitleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \Illuminate\Support\Carbon $match_date
 * @property string $league_name
 *
 * @method static LeagueMatchBuilder newQuery()
 * @method static LeagueMatchBuilder query()
 *
 * @property-read ?\App\Models\League $league
 *
 * @mixin LeagueMatchBuilder
 */
#[ObservedBy([UpdateLeagueTitleObserver::class])]
final class LeagueMatch extends Model
{
    /** @use HasFactory<\Database\Factories\LeagueMatchFactory> */
    use HasFactory;

    protected $table = 'matches';

    protected $with = [
        'league:id,name',
        'teamHome:id,name',
        'teamAway:id,name',
    ];

    protected $casts = [
        'match_date' => 'datetime',
    ];

    public function newEloquentBuilder($query): LeagueMatchBuilder
    {
        return new LeagueMatchBuilder($query);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function teamHome(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function teamAway(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}
