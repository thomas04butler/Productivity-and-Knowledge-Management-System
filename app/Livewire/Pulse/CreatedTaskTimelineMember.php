<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class CreatedTaskTimelineMember extends Card
{
    public function render()
    {
        return view('livewire.pulse.created-task-timeline-member');
    }
}
