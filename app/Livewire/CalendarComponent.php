<?php

namespace App\Livewire;

use App\Actions\Calendar\GenerateMonthGridAction;
use App\Builders\LeagueMatchBuilder;
use App\Data\MonthGridMetaData;
use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Team;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Class LivewireCalendar
 * @package Omnia\LivewireCalendar
 * @property Carbon $startsAt
 * @property Carbon $endsAt
 * @property Carbon $gridStartsAt
 * @property Carbon $gridEndsAt
 * @property int $weekStartsAt
 * @property int $weekEndsAt
 * @property string $currentDate
 */
final class CalendarComponent extends Component
{
    public $startsAt;

    public $endsAt;

    public $gridStartsAt;

    public $gridEndsAt;

    public $weekStartsAt;

    public $weekEndsAt;

    public $currentMatches = [];

    public $currentDate = '';

    public $openCurrentMatches;

    public $filters = [
        'league' => null,
        'team' => null,
    ];

    public array $leagues = [];

    public array $teams = [];

    public array $teamsFallback = [];

    protected $casts = [
        'startsAt' => 'date',
        'endsAt' => 'date',
        'gridStartsAt' => 'date',
        'gridEndsAt' => 'date',
    ];

    public function mount(): void
    {
        $startsAt = microtime(true);

        $this->leagues = League::query()->options()->toArray();
        $this->teams = Team::query()->options()->toArray();
        $this->teamsFallback = $this->teams;

        $this->weekStartsAt = CarbonInterface::SUNDAY;
        $this->weekEndsAt = CarbonInterface::SATURDAY;

        $initialYear = today()->year;
        $initialMonth = today()->month;

        $this->startsAt = Carbon::createFromDate($initialYear, $initialMonth, 1)->startOfDay();
        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();

        $this->calculateGridStartsEnds();

        $endsAt = microtime(true) - $startsAt;

        info(message: 'Page loaded in '.$endsAt.' ms');
    }

    public function goToPreviousMonth(): void
    {
        $this->startsAt->subMonthNoOverflow();
        $this->endsAt->subMonthNoOverflow();

        $this->calculateGridStartsEnds();
    }

    public function goToNextMonth(): void
    {
        $this->startsAt->addMonthNoOverflow();
        $this->endsAt->addMonthNoOverflow();

        $this->calculateGridStartsEnds();
    }

    public function goToCurrentMonth(): void
    {
        $this->startsAt = Carbon::today()->startOfMonth()->startOfDay();
        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();

        $this->calculateGridStartsEnds();
    }

    public function calculateGridStartsEnds(): void
    {
        $this->gridStartsAt = $this->startsAt->clone()->startOfWeek($this->weekStartsAt);
        $this->gridEndsAt = $this->endsAt->clone()->endOfWeek($this->weekEndsAt);

    }

    /**
     * @throws Exception
     */
    public function monthGrid(): Collection
    {
        return resolve(name: GenerateMonthGridAction::class)
            ->execute(
                new MonthGridMetaData(
                    firstDayOfGrid: $this->gridStartsAt,
                    lastDayOfGrid: $this->gridEndsAt,
                    league: $this->filters['league'],
                    team: $this->filters['team'],
                ),
            );
    }

    /**
     * @throws \Exception
     */
    public function applyFilters(): void
    {
        $league = League::query()->find($this->filters['league']);

        if (filled($league)) {
            $this->populateTeamsByLeague($league);
        } else {
            $this->teams = $this->teamsFallback;
        }

        $this->monthGrid();
    }

    private function populateTeamsByLeague(League $league): void
    {
        $this->teams = Team::query()
            ->where('league_id', $league->id)
            ->options()
            ->toArray();
    }

    public function events() : Collection
    {
        return collect();
    }

    public function onDayClick(string $year, string $month, string $day): void
    {
        $this->currentDate = $year.'-'.$month.'-'.$day;

        $this->currentMatches = LeagueMatch::query()
            ->applyLeagueFilter($this->filters['league'])
            ->applyTeamFilter($this->filters['team'])
            ->whereMatchDateBy($year, $month, $day)
            ->orderByDefaultWhen(
                condition: blank($this->filters['league']) && blank($this->filters['team'])
            )
            ->get();

        $this->openCurrentMatches = true;
    }

    /**
     * @throws Exception
     */
    public function render(): View
    {
        $events = $this->events();

        return view('livewire.calendar')
            ->with([
                'monthGrid' => $this->monthGrid(),
                'events' => $events,
            ]);
    }
}