<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class TasksWithinTimeFrame extends Card
{
    public function render()
    {
        return view('livewire.pulse.tasks-within-time-frame');
    }
}
