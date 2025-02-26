<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

final class CalendarForm extends Form
{
    #[Validate('nullable|exists:leagues,id|numeric|integer')]
    public $league;

    #[Validate('nullable|exists:teams,id|numeric|integer')]
    public $team;
}