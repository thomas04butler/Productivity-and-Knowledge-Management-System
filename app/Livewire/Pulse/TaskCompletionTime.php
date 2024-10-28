<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class TaskCompletionTime extends Card
{
    public function render()
    {
        return view('livewire.pulse.task-completion-time');
    }
}
