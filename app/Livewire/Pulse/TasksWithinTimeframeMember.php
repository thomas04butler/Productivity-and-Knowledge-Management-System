<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class TasksWithinTimeframeMember extends Card
{
    public function render()
    {
        return view('livewire.pulse.tasks-within-timeframe-member');
    }
}
