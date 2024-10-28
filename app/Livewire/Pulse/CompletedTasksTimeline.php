<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class CompletedTasksTimeline extends Card
{
    public function render()
    {
        return view('livewire.pulse.completed-tasks-timeline');
    }
}
