<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use Livewire\Component;
use Carbon\Carbon;

class Promo extends Component
{
    public $countdown = [];

    public function mount()
    {
        $this->calculateCountdown();
    }

    public function calculateCountdown()
    {
        $now = Carbon::now();

        $this->countdown = [
            'days' => 15, // Static value
            'hours' => $now->hour,
            'minutes' => $now->minute,
            'seconds' => $now->second
        ];
    }

    public function render()
    {
        $this->calculateCountdown();
        return view('livewire.landing-page.promo');
    }
}
