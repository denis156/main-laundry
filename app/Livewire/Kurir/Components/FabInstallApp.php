<?php

namespace App\Livewire\Kurir\Components;

use Livewire\Component;

class FabInstallApp extends Component
{
    public bool $installWebApp = false;

    public function openConfirmationInstallApp()
    {
        $this->installWebApp = true;
    }

    public function closeConfirmationInstall()
    {
        $this->installWebApp = false;
    }

    public function render()
    {
        return view('livewire.kurir.components.fab-install-app', [
            'appName' => config('app.name'),
            'appVersion' => config('app.kurir_version'),
        ]);
    }
}
