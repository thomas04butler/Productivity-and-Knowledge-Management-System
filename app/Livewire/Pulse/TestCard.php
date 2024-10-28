<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class TestCard extends Card
{
    public function render()
    {
        return view('livewire.pulse.test-card');
    }
}
