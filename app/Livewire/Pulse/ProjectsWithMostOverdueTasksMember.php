<?php

namespace App\Livewire\Pulse;

use Laravel\Pulse\Livewire\Card;

class ProjectsWithMostOverdueTasksMember extends Card
{
    public $analyticsUrl;

    public $userToken;

    public function mount()
    {
        // Define the analytics URL and user token here, or fetch them from a config, database, or any other source
        $this->analyticsUrl = route('analytics.projects.index');
        $this->userToken = request()->user()->createToken('auth-token')->plainTextToken;
    }

    public function render()
    {
        return view('livewire.pulse.projects-with-most-overdue-tasks-member', [
            'analyticsUrl' => $this->analyticsUrl,
            'userToken' => $this->userToken,
        ]);
    }
}
