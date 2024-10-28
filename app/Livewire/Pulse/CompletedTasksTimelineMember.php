<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class CompletedTasksTimelineMember extends Card
{
    public function render()
    {
        return view('livewire.pulse.completed-tasks-timeline-member');
    }
}
