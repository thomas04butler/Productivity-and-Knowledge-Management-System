<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class CompletedTasksTimelineLeader extends Card
{
    public function render()
    {
        return view('livewire.pulse.completed-tasks-timeline-leader');
    }
}
