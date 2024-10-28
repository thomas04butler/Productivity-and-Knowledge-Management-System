<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class CreatedTasksTimeline extends Card
{
    public function render()
    {
        return view('livewire.pulse.created-tasks-timeline');
    }
}
