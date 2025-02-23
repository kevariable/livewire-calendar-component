<?php

namespace App\Models;

use App\Builders\LeagueBuilder;
use Database\Factories\LeagueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 *
 * @method static LeagueBuilder newQuery()
 * @method static LeagueBuilder query()
 *
 * @mixin LeagueBuilder
 */
final  class League extends Model
{
    /** @use HasFactory<LeagueFactory> */
    use HasFactory;

    protected static function newFactory(): LeagueFactory
    {
        return LeagueFactory::new();
    }

    public function newEloquentBuilder($query): LeagueBuilder
    {
        return new LeagueBuilder($query);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class);
    }
}
