<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan\Components;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.pelanggan-guest')]
class SplashScreen extends Component
{
    /**
     * Skip splash screen dan langsung ke login
     */
    public function skipOnboarding(): void
    {
        // Set flag di localStorage via JavaScript
        $this->js(<<<'JS'
            localStorage.setItem('splash_screen_completed', 'true');
            window.location.href = '/pelanggan/masuk';
        JS);
    }

    /**
     * Finish splash screen dan redirect ke login
     */
    public function finishOnboarding(): void
    {
        // Set flag di localStorage via JavaScript
        $this->js(<<<'JS'
            localStorage.setItem('splash_screen_completed', 'true');
            window.location.href = '/pelanggan/masuk';
        JS);
    }

    public function render()
    {
        return view('livewire.pelanggan.components.splash-screen');
    }
}
