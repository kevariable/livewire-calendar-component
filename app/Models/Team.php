<?php

namespace App\Models;

use App\Builders\TeamBuilder;
use Database\Factories\LeagueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @method static TeamBuilder newQuery()
 * @method static TeamBuilder query()
 *
 * @mixin TeamBuilder
 */
final class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;


    protected $fillable = ['name', 'league_id'];

    public function newEloquentBuilder($query): TeamBuilder
    {
        return new TeamBuilder($query);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(related: League::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(related: LeagueMatch::class);
    }
}
