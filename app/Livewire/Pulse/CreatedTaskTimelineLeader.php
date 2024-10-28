<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class CreatedTaskTimelineLeader extends Card
{
    public function render()
    {
        return view('livewire.pulse.created-task-timeline-leader');
    }
}
